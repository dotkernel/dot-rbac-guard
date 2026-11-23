<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Factory;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function is_array;

class RbacGuardOptionsFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName): mixed
    {
        $config = $container->get('config')['dot_authorization'] ?? [];

        return new $requestedName(is_array($config) ? $config : []);
    }
}
