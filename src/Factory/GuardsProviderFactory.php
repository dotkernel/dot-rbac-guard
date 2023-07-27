<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Guard\Factory;
use Dot\Rbac\Guard\Guard\GuardPluginManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function is_string;

class GuardsProviderFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName, ?array $options = null): mixed
    {
        $options                  = $options ?? [];
        $options['guard_factory'] = isset($options['guard_factory'])
        && is_string($options['guard_factory'])
        && $container->has($options['guard_factory'])
            ? $container->get($options['guard_factory'])
            : new Factory($container, $container->get(GuardPluginManager::class));

        return new $requestedName($options);
    }
}
