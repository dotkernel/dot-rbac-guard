<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 5/21/2016
 * Time: 12:35 AM
 */

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Guard\Route\RouteGuard;
use Dot\Rbac\Role\RoleService;
use Interop\Container\ContainerInterface;

class RouteGuardFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = [])
    {
        $roleService = $container->get(RoleService::class);

        $moduleOptions = $container->get(RbacGuardOptions::class);
        if(!$moduleOptions instanceof RbacGuardOptions) {
            throw new RuntimeException('ModuleOptions is not of the required type');
        }

        $routeGuard = new RouteGuard($roleService, $options);
        $routeGuard->setProtectionPolicy($moduleOptions->getProtectionPolicy());

        return $routeGuard;
    }
}