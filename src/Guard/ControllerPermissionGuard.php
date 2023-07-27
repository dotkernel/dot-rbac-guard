<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Guard;

use Dot\Authorization\AuthorizationInterface;
use Dot\Controller\AbstractController;
use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ServerRequestInterface;

use function gettype;
use function in_array;
use function is_object;
use function sprintf;
use function strtolower;

class ControllerPermissionGuard extends AbstractGuard
{
    public const PRIORITY = 10;

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

        foreach ($rules as $rule) {
            $route       = strtolower($rule['route']);
            $actions     = isset($rule['actions']) ? (array) $rule['actions'] : [];
            $permissions = (array) $rule['permissions'];

            if (empty($actions)) {
                $this->rules[$route][0] = $permissions;
                continue;
            }

            foreach ($actions as $action) {
                $action                       = AbstractController::getMethodFromAction($action);
                $this->rules[$route][$action] = $permissions;
            }
        }
    }

    public function isGranted(ServerRequestInterface $request): bool
    {
        $routeResult = $request->getAttribute(RouteResult::class);
        if (! $routeResult instanceof RouteResult || ! $routeResult->getMatchedRouteName()) {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        $route = $routeResult->getMatchedRouteName();
        if (! isset($this->rules[$route])) {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        $params = $routeResult->getMatchedParams();
        $action = ! empty($params['action'])
            ? $params['action']
            : 'index';

        $action = AbstractController::getMethodFromAction($action);

        if (isset($this->rules[$route][$action])) {
            $allowedPermissions = $this->rules[$route][$action];
        } elseif (isset($this->rules[$route][0])) {
            $allowedPermissions = $this->rules[$route][0];
        } else {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        if (empty($allowedPermissions)) {
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
