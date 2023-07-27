<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Provider\GuardsProviderPluginManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class GuardsProviderPluginManagerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): GuardsProviderPluginManager
    {
        return new GuardsProviderPluginManager(
            $container,
            $container->get('config')['dot_authorization']['guards_provider_manager']
        );
    }
}
