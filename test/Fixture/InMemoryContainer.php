<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Fixture;

use Psr\Container\ContainerInterface;

use function array_key_exists;

/**
 * A minimal PSR-11 container that returns the services it was given.
 */
final class InMemoryContainer implements ContainerInterface
{
    /**
     * @param array<string, mixed> $services
     */
    public function __construct(private array $services = [])
    {
    }

    public function set(string $id, mixed $service): void
    {
        $this->services[$id] = $service;
    }

    /**
     * @throws ServiceNotFoundException
     */
    public function get(string $id): mixed
    {
        if (! array_key_exists($id, $this->services)) {
            throw new ServiceNotFoundException($id);
        }

        return $this->services[$id];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }
}
