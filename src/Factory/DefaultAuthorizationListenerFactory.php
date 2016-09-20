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
use Dot\Rbac\Guard\Provider\GuardsProviderInterface;
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
        $authentication = $container->has(AuthenticationInterface::class)
            ? $container->get(AuthenticationInterface::class)
            : null;

        return new DefaultAuthorizationListener(
            $container->get(GuardsProviderInterface::class),
            $authentication
        );
    }
}