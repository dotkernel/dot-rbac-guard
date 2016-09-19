<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 5/21/2016
 * Time: 12:35 AM
 */

namespace Dot\Rbac\Guard\Factory;

use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Guard\Route\RoutePermissionGuard;
use Interop\Container\ContainerInterface;

class RoutePermissionGuardFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = [])
    {
        $authorizationService = $container->get(AuthorizationInterface::class);
        
        $moduleOptions = $container->get(RbacGuardOptions::class);
        if(!$moduleOptions instanceof RbacGuardOptions) {
            throw new RuntimeException('ModuleOptions service is not of the required type');
        }

        $routeGuard = new RoutePermissionGuard($authorizationService, $options);
        $routeGuard->setProtectionPolicy($moduleOptions->getProtectionPolicy());

        return $routeGuard;
    }
}