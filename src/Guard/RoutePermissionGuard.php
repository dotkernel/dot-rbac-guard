<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Guard;

use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Psr\Http\Message\ServerRequestInterface;
use Mezzio\Router\RouteResult;

/**
 * Class RoutePermissionGuard
 * @package Dot\Rbac\Guard\Route
 */
class RoutePermissionGuard extends AbstractGuard
{
    const PRIORITY = 70;

    /**
     * @var AuthorizationInterface
     */
    protected $authorizationService;

    /**\
     * RoutePermissionGuard constructor.
     * @param array $options
     */
    public function __construct(array $options = null)
    {
        $options = $options ?? [];
        parent::__construct($options);

        if (isset($options['authorization_service'])
            && $options['authorization_service'] instanceof AuthorizationInterface
        ) {
            $this->setAuthorizationService($options['authorization_service']);
        }

        if (!$this->authorizationService instanceof AuthorizationInterface) {
            throw new RuntimeException('Authorization service is required by this guard and was not set');
        }
    }

    /**
     * @param array $rules
     */
    public function setRules(array $rules)
    {
        $this->rules = [];
        foreach ($rules as $key => $value) {
            if (is_int($key)) {
                $routeName = strtolower($value);
                $permissions = [];
            } else {
                $routeName = strtolower($key);
                $permissions = (array)$value;
            }
            $this->rules[$routeName] = $permissions;
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function isGranted(ServerRequestInterface $request): bool
    {
        $routeResult = $request->getAttribute(RouteResult::class, false);
        //if we dont have a matched route(probably 404 not found) let it go to the final handler
        if (!$routeResult instanceof RouteResult) {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        $matchedRouteName = strtolower($routeResult->getMatchedRouteName());
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
        $condition = $allowedPermissions['condition'] ?? GuardInterface::CONDITION_AND;

        if (GuardInterface::CONDITION_AND === $condition) {
            foreach ($permissions as $permission) {
                if (!$this->getAuthorizationService()->isGranted($permission)) {
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
            is_object($condition) ? get_class($condition) : gettype($condition)
        ));
    }

    /**
     * @return AuthorizationInterface
     */
    public function getAuthorizationService(): AuthorizationInterface
    {
        return $this->authorizationService;
    }

    /**
     * @param AuthorizationInterface $authorizationService
     */
    public function setAuthorizationService(AuthorizationInterface $authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }
}
