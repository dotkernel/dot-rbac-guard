<?php

/**
 * see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Guard\GuardInterface;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Role\RoleServiceInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function in_array;
use function is_array;
use function is_string;

class GuardFactory
{
    /**
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName, ?array $options = null)
    {
        $options                 = $options ?? [];
        $options['role_service'] = isset($options['role_service'])
        && is_string($options['role_service'])
        && $container->has($options['role_service'])
            ? $container->get($options['role_service'])
            : $container->get(RoleServiceInterface::class);

        /** @var RbacGuardOptions $moduleOptions */
        $moduleOptions                = $container->get(RbacGuardOptions::class);
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
