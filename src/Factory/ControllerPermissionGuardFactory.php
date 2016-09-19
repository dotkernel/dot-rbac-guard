<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/27/2016
 * Time: 11:40 PM
 */

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Controller\ControllerPermissionGuard;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Interop\Container\ContainerInterface;
use N3vrax\DkAuthorization\AuthorizationInterface;

class ControllerPermissionGuardFactory
{
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