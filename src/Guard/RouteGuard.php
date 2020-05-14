<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Guard;

use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Role\RoleServiceInterface;
use Psr\Http\Message\ServerRequestInterface;
use Mezzio\Router\RouteResult;

/**
 * Class RouteGuard
 * @package Dot\Rbac\Guard\Route
 */
class RouteGuard extends AbstractGuard
{
    const PRIORITY = 100;

    /**
     * @var RoleServiceInterface
     */
    protected $roleService;

    /**
     * RouteGuard constructor.
     * @param array $options
     */
    public function __construct(array $options = null)
    {
        $options = $options ?? [];
        parent::__construct($options);

        if (isset($options['role_service']) && $options['role_service'] instanceof RoleServiceInterface) {
            $this->setRoleService($options['role_service']);
        }

        if (!$this->roleService instanceof RoleServiceInterface) {
            throw new RuntimeException('RoleService is required by this guard and was not set');
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
                $roles = [];
            } else {
                $routeName = strtolower($key);
                $roles = (array)$value;
            }

            $this->rules[$routeName] = $roles;
        }
    }

    /**
     * @return RoleServiceInterface
     */
    public function getRoleService(): RoleServiceInterface
    {
        return $this->roleService;
    }

    /**
     * @param RoleServiceInterface $roleService
     */
    public function setRoleService(RoleServiceInterface $roleService)
    {
        $this->roleService = $roleService;
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
        $allowedRoles = null;

        foreach (array_keys($this->rules) as $routeName) {
            if ($routeName === $matchedRouteName) {
                $allowedRoles = $this->rules[$routeName];
                break;
            }
        }

        if (null === $allowedRoles) {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        if (in_array('*', $allowedRoles)) {
            return true;
        }

        return $this->roleService->matchIdentityRoles($allowedRoles);
    }
}
