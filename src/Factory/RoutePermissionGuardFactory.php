<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 12:35 AM
 */

namespace Dot\Rbac\Guard\Factory;

use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Guard\Route\RoutePermissionGuard;
use Interop\Container\ContainerInterface;

/**
 * Class RoutePermissionGuardFactory
 * @package Dot\Rbac\Guard\Factory
 */
class RoutePermissionGuardFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array $options
     * @return RoutePermissionGuard
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = [])
    {
        $authorizationService = $container->get(AuthorizationInterface::class);
        $moduleOptions = $container->get(RbacGuardOptions::class);

        $routeGuard = new RoutePermissionGuard($authorizationService, $options);
        $routeGuard->setProtectionPolicy($moduleOptions->getProtectionPolicy());

        return $routeGuard;
    }
}
