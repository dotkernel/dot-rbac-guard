<?php
/**
 * @copyright: DotKernel
 * @library: dot-rbac-guard
 * @author: n3vrax
 * Date: 2/1/2017
 * Time: 4:43 AM
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Exception\RuntimeException;
use Interop\Container\ContainerInterface;

/**
 * Class Factory
 * @package Dot\Rbac\Guard\Provider
 */
class Factory
{
    /** @var  ContainerInterface */
    protected $container;

    /** @var  GuardsProviderPluginManager */
    protected $guardsProviderPluginManager;

    /**
     * Factory constructor.
     * @param ContainerInterface $container
     * @param GuardsProviderPluginManager|null $guardsProviderPluginManager
     */
    public function __construct(
        ContainerInterface $container,
        GuardsProviderPluginManager $guardsProviderPluginManager = null
    ) {
        $this->container = $container;
        $this->guardsProviderPluginManager = $guardsProviderPluginManager;
    }

    /**
     * @param array $specs
     * @return GuardsProviderInterface
     */
    public function create(array $specs) : GuardsProviderInterface
    {
        $type = $specs['type'] ?? '';
        if (empty($type)) {
            throw new RuntimeException('Guard provider type was not specified');
        }

        return $this->getGuardsProviderPluginManager()->get($type, $specs['options'] ?? []);
    }

    /**
     * @return GuardsProviderPluginManager
     */
    public function getGuardsProviderPluginManager() : GuardsProviderPluginManager
    {
        if (! $this->guardsProviderPluginManager) {
            $this->guardsProviderPluginManager = new GuardsProviderPluginManager($this->container, []);
        }

        return $this->guardsProviderPluginManager;
    }
}
