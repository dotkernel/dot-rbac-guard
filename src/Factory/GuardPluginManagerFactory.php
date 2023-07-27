<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Guard\GuardPluginManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class GuardPluginManagerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): GuardPluginManager
    {
        $config = $container->get('config');
        $config = $config['dot_authorization']['guard_manager'];

        return new GuardPluginManager($container, $config);
    }
}
