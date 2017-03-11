<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

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
