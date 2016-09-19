<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/27/2016
 * Time: 11:37 PM
 */

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Controller\ControllerGuard;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Interop\Container\ContainerInterface;
use N3vrax\DkRbac\Role\RoleService;

class ControllerGuardFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = [])
    {
        $roleService = $container->get(RoleService::class);
        /** @var RbacGuardOptions $moduleOptions */
        $moduleOptions = $container->get(RbacGuardOptions::class);

        $controllerGuard = new ControllerGuard($roleService, $options);
        $controllerGuard->setProtectionPolicy($moduleOptions->getProtectionPolicy());

        return $controllerGuard;
    }
}