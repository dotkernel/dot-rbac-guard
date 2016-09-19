<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 5/21/2016
 * Time: 2:48 AM
 */

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\GuardPluginManager;
use Dot\Rbac\Guard\GuardsProvider;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Interop\Container\ContainerInterface;

class GuardsProviderFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $options = $container->get(RbacGuardOptions::class);
        if(!$options instanceof RbacGuardOptions) {
            throw new RuntimeException("ModuleOptions service is not of the required type");
        }

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