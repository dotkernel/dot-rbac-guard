<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 6/22/2016
 * Time: 4:42 PM
 */

namespace Dot\Rbac\Guard\Factory;

use Dot\Authentication\AuthenticationInterface;
use Dot\Rbac\Guard\Listener\DefaultAuthorizationListener;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Guard\Provider\GuardsProviderPluginManager;
use Interop\Container\ContainerInterface;

/**
 * Class DefaultAuthorizationListenerFactory
 * @package Dot\Rbac\Guard\Factory
 */
class DefaultAuthorizationListenerFactory
{
    /**
     * @param ContainerInterface $container
     * @return DefaultAuthorizationListener
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var RbacGuardOptions $options */
        $options = $container->get(RbacGuardOptions::class);
        /** @var GuardsProviderPluginManager $guardsProviderManager */
        $guardsProviderManager = $container->get(GuardsProviderPluginManager::class);

        $guardsProviderConfig = $options->getGuardsProvider();
        $guardsProvider = $guardsProviderManager->get(key($guardsProviderConfig), current($guardsProviderConfig));

        $authentication = $container->has(AuthenticationInterface::class)
            ? $container->get(AuthenticationInterface::class)
            : null;

        return new DefaultAuthorizationListener(
            $guardsProvider,
            $options,
            $authentication
        );
    }
}