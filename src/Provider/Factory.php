<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Exception\RuntimeException;
use Psr\Container\ContainerInterface;

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

    public function create(array $specs): GuardsProviderInterface
    {
        $type = $specs['type'] ?? '';
        if (empty($type)) {
            throw new RuntimeException('Guard provider type was not specified');
        }

        return $this->getGuardsProviderPluginManager()->get($type, $specs['options'] ?? null);
    }

    public function getGuardsProviderPluginManager(): GuardsProviderPluginManager
    {
        if (! $this->guardsProviderPluginManager) {
            $this->guardsProviderPluginManager = new GuardsProviderPluginManager($this->container, []);
        }

        return $this->guardsProviderPluginManager;
    }
}
