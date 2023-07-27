<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Guard;

use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ServerRequestInterface;

use function array_keys;
use function gettype;
use function in_array;
use function is_int;
use function is_object;
use function sprintf;
use function strtolower;

class RoutePermissionGuard extends AbstractGuard
{
    public const PRIORITY = 70;

    protected ?AuthorizationInterface $authorizationService = null;

    public function __construct(?array $options = null)
    {
        $options = $options ?? [];
        parent::__construct($options);

        if (
            isset($options['authorization_service'])
            && $options['authorization_service'] instanceof AuthorizationInterface
        ) {
            $this->setAuthorizationService($options['authorization_service']);
        }

        if (! $this->authorizationService instanceof AuthorizationInterface) {
            throw new RuntimeException('Authorization service is required by this guard and was not set');
        }
    }

    public function setRules(array $rules): void
    {
        $this->rules = [];
        foreach ($rules as $key => $value) {
            if (is_int($key)) {
                $routeName   = strtolower($value);
                $permissions = [];
            } else {
                $routeName   = strtolower($key);
                $permissions = (array) $value;
            }
            $this->rules[$routeName] = $permissions;
        }
    }

    public function isGranted(ServerRequestInterface $request): bool
    {
        $routeResult = $request->getAttribute(RouteResult::class, false);
        //if we do not have a matched route(probably 404 not found) let it go to the final handler
        if (! $routeResult instanceof RouteResult || ! $routeResult->getMatchedRouteName()) {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        $matchedRouteName   = strtolower($routeResult->getMatchedRouteName());
        $allowedPermissions = null;

        foreach (array_keys($this->rules) as $routeName) {
            if ($matchedRouteName === $routeName) {
                $allowedPermissions = $this->rules[$routeName];
                break;
            }
        }

        // If no rules apply, it is considered as granted or not based on the protection policy
        if (null === $allowedPermissions) {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        if (in_array('*', $allowedPermissions)) {
            return true;
        }

        $permissions = $allowedPermissions['permissions'] ?? $allowedPermissions;
        $condition   = $allowedPermissions['condition'] ?? GuardInterface::CONDITION_AND;

        if (GuardInterface::CONDITION_AND === $condition) {
            foreach ($permissions as $permission) {
                if (! $this->getAuthorizationService()->isGranted($permission)) {
                    return false;
                }
            }
            return true;
        }

        if (GuardInterface::CONDITION_OR === $condition) {
            foreach ($permissions as $permission) {
                if ($this->getAuthorizationService()->isGranted($permission)) {
                    return true;
                }
            }
            return false;
        }

        throw new InvalidArgumentException(sprintf(
            'Condition must be either "AND" or "OR", %s given',
            is_object($condition) ? $condition::class : gettype($condition)
        ));
    }

    public function getAuthorizationService(): ?AuthorizationInterface
    {
        return $this->authorizationService;
    }

    public function setAuthorizationService(AuthorizationInterface $authorizationService): void
    {
        $this->authorizationService = $authorizationService;
    }
}
