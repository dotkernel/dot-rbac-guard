<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Guard;

use Dot\Controller\AbstractController;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Role\RoleServiceInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouteResult;

/**
 * Class ControllerGuard
 * @package Dot\Rbac\Guard\Controller
 */
class ControllerGuard extends AbstractGuard
{
    const PRIORITY = 40;

    /** @var RoleServiceInterface */
    protected $roleService;

    /**
     * ControllerGuard constructor.
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

        foreach ($rules as $rule) {
            $route = strtolower($rule['route']);
            $actions = isset($rule['actions']) ? (array)$rule['actions'] : [];
            $roles = (array)$rule['roles'];

            if (empty($actions)) {
                $this->rules[$route][0] = $roles;
                continue;
            }

            foreach ($actions as $action) {
                $action = AbstractController::getMethodFromAction($action);
                $this->rules[$route][$action] = $roles;
            }
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
        $routeResult = $request->getAttribute(RouteResult::class, null);
        if (!$routeResult instanceof RouteResult) {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        $route = strtolower($routeResult->getMatchedRouteName());

        if (!isset($this->rules[$route])) {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        $params = $routeResult->getMatchedParams();
        $action = isset($params['action']) && !empty($params['action'])
            ? $params['action']
            : 'index';

        $action = AbstractController::getMethodFromAction($action);

        if (isset($this->rules[$route][$action])) {
            $allowedRoles = $this->rules[$route][$action];
        } elseif (isset($this->rules[$route][0])) {
            $allowedRoles = $this->rules[$route][0];
        } else {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        if (in_array('*', $allowedRoles)) {
            return true;
        }

        return $this->roleService->matchIdentityRoles($allowedRoles);
    }
}
