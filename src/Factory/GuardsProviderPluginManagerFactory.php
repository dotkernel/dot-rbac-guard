<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 12:45 AM
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Provider\GuardsProviderPluginManager;
use Interop\Container\ContainerInterface;

/**
 * Class GuardsProviderPluginManagerFactory
 * @package Dot\Rbac\Guard\Factory
 */
class GuardsProviderPluginManagerFactory
{
    /**
     * @param ContainerInterface $container
     * @return GuardsProviderPluginManager
     */
    public function __invoke(ContainerInterface $container)
    {
        return new GuardsProviderPluginManager(
            $container,
            $container->get('config')['dot_authorization']['guards_provider_manager']
        );
    }
}
