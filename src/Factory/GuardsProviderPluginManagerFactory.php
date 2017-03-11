<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
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
