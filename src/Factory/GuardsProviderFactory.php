<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Guard\Factory;
use Dot\Rbac\Guard\Guard\GuardPluginManager;
use Dot\Rbac\Guard\Provider\ArrayGuardsProvider;
use Psr\Container\ContainerInterface;

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
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $options ?? [];
        $options['guard_factory'] = isset($options['guard_factory'])
        && is_string($options['guard_factory'])
        && $container->has($options['guard_factory'])
            ? $container->get($options['guard_factory'])
            : new Factory($container, $container->get(GuardPluginManager::class));

        return new $requestedName($options);
    }
}
