<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 2:48 AM
 */

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\GuardPluginManager;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Guard\Provider\GuardsProvider;
use Interop\Container\ContainerInterface;

/**
 * Class GuardsProviderFactory
 * @package Dot\Rbac\Guard\Factory
 */
class GuardsProviderFactory
{
    /**
     * @param ContainerInterface $container
     * @return GuardsProvider
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var RbacGuardOptions $options */
        $options = $container->get(RbacGuardOptions::class);
        $guardsOptions = $options->getGuards();

        if(empty($guardsOptions)) {
            return new GuardsProvider([]);
        }

        $pluginManager = $container->get(GuardPluginManager::class);
        $guards = [];

        foreach ($guardsOptions as $type => $options) {
            $guards[] = $pluginManager->get($type, $options);
        }

        return new GuardsProvider($guards);
    }
}