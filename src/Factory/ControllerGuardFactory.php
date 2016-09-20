<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 6/27/2016
 * Time: 11:37 PM
 */

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Controller\ControllerGuard;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Role\RoleService;
use Interop\Container\ContainerInterface;

/**
 * Class ControllerGuardFactory
 * @package Dot\Rbac\Guard\Factory
 */
class ControllerGuardFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array $options
     * @return ControllerGuard
     */
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