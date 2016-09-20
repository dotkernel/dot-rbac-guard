<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 12:35 AM
 */

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Guard\Route\RouteGuard;
use Dot\Rbac\Role\RoleService;
use Interop\Container\ContainerInterface;

/**
 * Class RouteGuardFactory
 * @package Dot\Rbac\Guard\Factory
 */
class RouteGuardFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array $options
     * @return RouteGuard
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = [])
    {
        $roleService = $container->get(RoleService::class);
        /** @var  RbacGuardOptions */
        $rbacGuardOptions = $container->get(RbacGuardOptions::class);

        $routeGuard = new RouteGuard($roleService, $options);
        $routeGuard->setProtectionPolicy($rbacGuardOptions->getProtectionPolicy());

        return $routeGuard;
    }
}