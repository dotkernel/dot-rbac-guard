<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 5/21/2016
 * Time: 12:38 AM
 */

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\GuardPluginManager;
use Interop\Container\ContainerInterface;

class GuardPluginManagerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');
        $config = $config['dk_authorization']['guard_manager'];

        $pluginManager = new GuardPluginManager($container, $config);

        return $pluginManager;
    }
}