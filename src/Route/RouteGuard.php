<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/20/2016
 * Time: 8:46 PM
 */

namespace Dot\Rbac\Guard\Route;

use Dot\Rbac\Guard\GuardInterface;
use Dot\Rbac\Guard\ProtectionPolicyTrait;
use Dot\Rbac\Role\RoleServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouteResult;

/**
 * Class RouteGuard
 * @package Dot\Rbac\Guard\Route
 */
class RouteGuard implements GuardInterface
{
    use ProtectionPolicyTrait;

    const PRIORITY = 100;

    /**
     * @var RoleServiceInterface
     */
    protected $roleService;

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * RouteGuard constructor.
     * @param RoleServiceInterface $roleService
     * @param array $rules
     */
    public function __construct(RoleServiceInterface $roleService, array $rules = [])
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

        foreach ($rules as $key => $value) {
            if (is_int($key)) {
                $routeRegex = $value;
                $roles = [];
            } else {
                $routeRegex = $key;
                $roles = (array)$value;
            }

            $this->rules[$routeRegex] = $roles;
        }
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return self::PRIORITY;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return bool
     */
    public function isGranted(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeResult = $request->getAttribute(RouteResult::class, false);
        //if we dont have a matched route(probably 404 not found) let it go to the final handler
        if (!$routeResult instanceof RouteResult) {
            return true;
        }

        $routeName = $routeResult->getMatchedRouteName();
        $allowedRoles = null;

        foreach (array_keys($this->rules) as $routeRule) {
            if (fnmatch($routeRule, $routeName, FNM_CASEFOLD)) {
                $allowedRoles = $this->rules[$routeRule];
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