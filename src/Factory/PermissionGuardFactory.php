<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Factory;

use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Guard\GuardInterface;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Psr\Container\ContainerInterface;

/**
 * Class PermissionGuardFactory
 * @package Dot\Rbac\Guard\Factory
 */
class PermissionGuardFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $options ?? [];
        $options['authorization_service'] = isset($options['authorization_service'])
        && is_string($options['authorization_service'])
        && $container->has($options['authorization_service'])
            ? $container->get($options['authorization_service'])
            : $container->get(AuthorizationInterface::class);

        /** @var RbacGuardOptions $moduleOptions */
        $moduleOptions = $container->get(RbacGuardOptions::class);
        $options['protection_policy'] = isset($options['protection_policy'])
        && in_array($options['protection_policy'], [GuardInterface::POLICY_ALLOW, GuardInterface::POLICY_DENY])
            ? $options['protection_policy']
            : $moduleOptions->getProtectionPolicy();

        $options['rules'] = isset($options['rules']) && is_array($options['rules'])
            ? $options['rules']
            : [];

        return new $requestedName($options);
    }
}
