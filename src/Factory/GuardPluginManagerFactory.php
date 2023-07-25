<?php

/**
 * see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Guard\GuardPluginManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class GuardPluginManagerFactory
{
    /**
     * @return GuardPluginManager
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');
        $config = $config['dot_authorization']['guard_manager'];

        return new GuardPluginManager($container, $config);
    }
}
