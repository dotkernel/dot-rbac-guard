<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/20/2016
 * Time: 8:46 PM
 */

namespace Dot\Rbac\Guard\Route;

use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Dot\Rbac\Guard\GuardInterface;
use Dot\Rbac\Guard\ProtectionPolicyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouteResult;

/**
 * Class RoutePermissionGuard
 * @package Dot\Rbac\Guard\Route
 */
class RoutePermissionGuard implements GuardInterface
{
    use ProtectionPolicyTrait;

    const PRIORITY = 70;

    /**
     * @var AuthorizationInterface
     */
    protected $authorizationService;

    /**
     * @var array
     */
    protected $rules = [];

    /**\
     * RoutePermissionGuard constructor.
     * @param AuthorizationInterface $authorizationService
     * @param array $rules
     */
    public function __construct(AuthorizationInterface $authorizationService, array $rules = [])
    {
        $this->authorizationService = $authorizationService;
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
                $permissions = [];
            } else {
                $routeRegex = $key;
                $permissions = (array)$value;
            }
            $this->rules[$routeRegex] = $permissions;
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
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        $routeName = $routeResult->getMatchedRouteName();
        $allowedPermissions = null;

        foreach (array_keys($this->rules) as $routeRule) {
            if (fnmatch($routeRule, $routeName, FNM_CASEFOLD)) {
                $allowedPermissions = $this->rules[$routeRule];
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
        $permissions = isset($allowedPermissions['permissions'])
            ? $allowedPermissions['permissions']
            : $allowedPermissions;

        $condition = isset($allowedPermissions['condition'])
            ? $allowedPermissions['condition']
            : GuardInterface::CONDITION_AND;

        if (GuardInterface::CONDITION_AND === $condition) {
            foreach ($permissions as $permission) {
                if (!$this->authorizationService->isGranted($permission)) {
                    return false;
                }
            }
            return true;
        }

        if (GuardInterface::CONDITION_OR === $condition) {
            foreach ($permissions as $permission) {
                if ($this->authorizationService->isGranted($permission)) {
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
}
