<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 12:45 AM
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Guard\Factory;
use Dot\Rbac\Guard\Guard\GuardPluginManager;
use Dot\Rbac\Guard\Provider\ArrayGuardsProvider;
use Interop\Container\ContainerInterface;

/**
 * Class ArrayGuardsProviderFactory
 * @package Dot\Rbac\Guard\Factory
 */
class GuardsProviderFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array $options
     * @return ArrayGuardsProvider
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = [])
    {
        $options['guard_factory'] = isset($options['guard_factory'])
            && is_string($options['guard_factory'])
            && $container->has($options['guard_factory'])
            ? $container->get($options['guard_factory'])
            : new Factory($container, $container->get(GuardPluginManager::class));

        return new $requestedName($options);
    }
}
