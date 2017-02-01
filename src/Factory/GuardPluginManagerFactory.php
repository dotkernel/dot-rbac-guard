<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 12:38 AM
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Guard\GuardPluginManager;
use Interop\Container\ContainerInterface;

/**
 * Class GuardPluginManagerFactory
 * @package Dot\Rbac\Guard\Factory
 */
class GuardPluginManagerFactory
{
    /**
     * @param ContainerInterface $container
     * @return GuardPluginManager
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');
        $config = $config['dot_authorization']['guard_manager'];

        return new GuardPluginManager($container, $config);
    }
}
