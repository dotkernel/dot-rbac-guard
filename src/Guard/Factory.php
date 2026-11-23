<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Guard;

use Dot\Rbac\Guard\Exception\RuntimeException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use function is_array;
use function is_string;

class Factory
{
    protected ContainerInterface $container;

    protected ?GuardPluginManager $guardPluginManager;

    public function __construct(ContainerInterface $container, ?GuardPluginManager $guardPluginManager = null)
    {
        $this->container          = $container;
        $this->guardPluginManager = $guardPluginManager;
    }

    /**
     * @param array<array-key, mixed> $specs
     * @throws ContainerExceptionInterface
     */
    public function create(array $specs): GuardInterface
    {
        $type    = $specs['type'] ?? '';
        $options = $specs['options'] ?? null;
        if (! is_string($type) || $type === '') {
            throw new RuntimeException('Guard type was not provided');
        }

        $guardPluginManager = $this->getGuardPluginManager();
        return $guardPluginManager->build($type, is_array($options) ? $options : null);
    }

    public function getGuardPluginManager(): GuardPluginManager
    {
        if ($this->guardPluginManager === null) {
            $this->guardPluginManager = new GuardPluginManager($this->container, []);
        }

        return $this->guardPluginManager;
    }
}
