<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/27/2016
 * Time: 8:02 PM
 */

namespace Dot\Rbac\Guard\Controller;

use Dot\Controller\AbstractActionController;
use Dot\Controller\AbstractController;
use Dot\Rbac\Guard\GuardInterface;
use Dot\Rbac\Guard\ProtectionPolicyTrait;
use Dot\Rbac\Role\RoleService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouteResult;

class ControllerGuard implements GuardInterface
{
    use ProtectionPolicyTrait;

    const PRIORITY = 25;

    /** @var RoleService  */
    protected $roleService;

    /** @var array  */
    protected $rules = [];

    /**
     * ControllerGuard constructor.
     * @param RoleService $roleService
     * @param array $rules
     */
    public function __construct(RoleService $roleService, array $rules = [])
    {
        $this->roleService = $roleService;
        $this->setRules($rules);
    }

    /**
     * @param array $rules
     */
    public function setRules(array $rules)
    {
        $this->rules = [];

        foreach ($rules as $rule) {
            $route = strtolower($rule['route']);
            $actions = isset($rule['actions']) ? (array) $rule['actions'] : [];
            $roles = (array) $rule['roles'];

            if(empty($actions)) {
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
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return bool
     */
    public function isGranted(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeResult = $request->getAttribute(RouteResult::class, null);
        if(!$routeResult instanceof RouteResult) {
            return true;
        }

        $controller = $routeResult->getMatchedMiddleware();
        if(is_array($controller))
            $controller = current($controller);
        
        if(is_subclass_of($controller, AbstractActionController::class))
        {
            $route = $routeResult->getMatchedRouteName();
            $params = $routeResult->getMatchedParams();
            $action = isset($params['action']) && !empty($params['action'])
                ? $params['action']
                : 'index';

            $action = AbstractController::getMethodFromAction($action);

            if(!isset($this->rules[$route])) {
                return $this->protectionPolicy === self::POLICY_ALLOW;
            }

            if(isset($this->rules[$route][$action])) {
                $allowedRoles = $this->rules[$route][$action];
            }
            elseif(isset($this->rules[$route][0])) {
                $allowedRoles = $this->rules[$route][0];
            }
            else {
                return $this->protectionPolicy === self::POLICY_ALLOW;
            }

            if(in_array('*', $allowedRoles)) {
                return true;
            }

            return $this->roleService->matchIdentityRoles($allowedRoles);
        }

        //if not an AbstractController, this guard will skip
        return true;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return static::PRIORITY;
    }
}