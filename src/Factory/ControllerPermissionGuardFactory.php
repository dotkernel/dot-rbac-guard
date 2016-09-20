<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 6/27/2016
 * Time: 11:40 PM
 */

namespace Dot\Rbac\Guard\Factory;

use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Controller\ControllerPermissionGuard;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Interop\Container\ContainerInterface;

/**
 * Class ControllerPermissionGuardFactory
 * @package Dot\Rbac\Guard\Factory
 */
class ControllerPermissionGuardFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array $options
     * @return ControllerPermissionGuard
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = [])
    {
        $authorizationService = $container->get(AuthorizationInterface::class);
        /** @var RbacGuardOptions $moduleOptions */
        $moduleOptions = $container->get(RbacGuardOptions::class);

        $guard = new ControllerPermissionGuard($authorizationService, $options);
        $guard->setProtectionPolicy($moduleOptions->getProtectionPolicy());

        return $guard;
    }
}