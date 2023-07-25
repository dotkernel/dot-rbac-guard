<?php

/**
 * see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Provider\GuardsProviderPluginManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class GuardsProviderPluginManagerFactory
{
    /**
     * @return GuardsProviderPluginManager
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        return new GuardsProviderPluginManager(
            $container,
            $container->get('config')['dot_authorization']['guards_provider_manager']
        );
    }
}
