<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Exception\RuntimeException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use function is_array;
use function is_string;

class Factory
{
    protected ContainerInterface $container;

    protected ?GuardsProviderPluginManager $guardsProviderPluginManager;

    public function __construct(
        ContainerInterface $container,
        ?GuardsProviderPluginManager $guardsProviderPluginManager = null
    ) {
        $this->container                   = $container;
        $this->guardsProviderPluginManager = $guardsProviderPluginManager;
    }

    /**
     * @param array<array-key, mixed> $specs
     * @throws ContainerExceptionInterface
     */
    public function create(array $specs): GuardsProviderInterface
    {
        $type    = $specs['type'] ?? '';
        $options = $specs['options'] ?? null;
        if (! is_string($type) || $type === '') {
            throw new RuntimeException('Guard provider type was not specified');
        }

        return $this->getGuardsProviderPluginManager()->build($type, is_array($options) ? $options : null);
    }

    public function getGuardsProviderPluginManager(): GuardsProviderPluginManager
    {
        if ($this->guardsProviderPluginManager === null) {
            $this->guardsProviderPluginManager = new GuardsProviderPluginManager($this->container, []);
        }

        return $this->guardsProviderPluginManager;
    }
}
