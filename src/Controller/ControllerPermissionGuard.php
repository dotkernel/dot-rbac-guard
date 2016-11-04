<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 6/27/2016
 * Time: 8:02 PM
 */

namespace Dot\Rbac\Guard\Controller;

use Dot\Authorization\AuthorizationInterface;
use Dot\Controller\AbstractActionController;
use Dot\Controller\AbstractController;
use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Dot\Rbac\Guard\GuardInterface;
use Dot\Rbac\Guard\ProtectionPolicyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouteResult;

/**
 * Class ControllerPermissionGuard
 * @package Dot\Rbac\Guard\Controller
 */
class ControllerPermissionGuard implements GuardInterface
{
    use ProtectionPolicyTrait;

    const PRIORITY = 10;

    /** @var AuthorizationInterface */
    protected $authorization;

    /** @var array */
    protected $rules = [];

    /**
     * ControllerPermissionGuard constructor.
     * @param AuthorizationInterface $authorization
     * @param array $rules
     */
    public function __construct(AuthorizationInterface $authorization, array $rules = [])
    {
        $this->authorization = $authorization;
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
            $actions = isset($rule['actions']) ? (array)$rule['actions'] : [];
            $permissions = (array)$rule['permissions'];

            if (empty($actions)) {
                $this->rules[$route][0] = $permissions;
                continue;
            }

            foreach ($actions as $action) {
                $action = AbstractController::getMethodFromAction($action);
                $this->rules[$route][$action] = $permissions;
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
        if (!$routeResult instanceof RouteResult) {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        /**
         * check if at least one Controller is in the middleware stack
         */
        $middleware = $routeResult->getMatchedMiddleware();
        $controller = null;
        if (is_array($middleware)) {
            foreach ($middleware as $m) {
                if (is_subclass_of($m, AbstractActionController::class)) {
                    $controller = $m;
                    break;
                }
            }
        } else {
            $controller = is_subclass_of($middleware, AbstractActionController::class) ? $middleware : null;
        }

        if ($controller) {
            $route = $routeResult->getMatchedRouteName();
            $params = $routeResult->getMatchedParams();
            $action = isset($params['action']) && !empty($params['action'])
                ? $params['action']
                : 'index';

            $action = AbstractController::getMethodFromAction($action);

            if (!isset($this->rules[$route])) {
                return $this->protectionPolicy === self::POLICY_ALLOW;
            }

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

            $permissions = isset($allowedPermissions['permissions'])
                ? $allowedPermissions['permissions']
                : $allowedPermissions;

            $condition = isset($allowedPermissions['condition'])
                ? $allowedPermissions['condition']
                : GuardInterface::CONDITION_AND;

            if (GuardInterface::CONDITION_AND === $condition) {
                foreach ($permissions as $permission) {
                    if (!$this->authorization->isGranted($permission)) {
                        return false;
                    }
                }
                return true;
            }

            if (GuardInterface::CONDITION_OR === $condition) {
                foreach ($permissions as $permission) {
                    if ($this->authorization->isGranted($permission)) {
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

        //if not an AbstractController, this guard will skip
        return $this->protectionPolicy === self::POLICY_ALLOW;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return static::PRIORITY;
    }
}