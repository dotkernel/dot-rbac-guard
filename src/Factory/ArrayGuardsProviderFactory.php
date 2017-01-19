<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 12:45 AM
 */

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\GuardPluginManager;
use Dot\Rbac\Guard\Provider\ArrayGuardsProvider;
use Interop\Container\ContainerInterface;

/**
 * Class ArrayGuardsProviderFactory
 * @package Dot\Rbac\Guard\Factory
 */
class ArrayGuardsProviderFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array $guardsConfig
     * @return ArrayGuardsProvider
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $guardsConfig = null)
    {
        $guardManager = $container->get(GuardPluginManager::class);
        return new ArrayGuardsProvider($guardManager, $guardsConfig);
    }
}
