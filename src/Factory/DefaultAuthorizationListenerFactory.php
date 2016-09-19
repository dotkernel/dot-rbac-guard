<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/22/2016
 * Time: 4:42 PM
 */

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\GuardsProvider;
use Dot\Rbac\Guard\Listener\DefaultAuthorizationListener;
use Interop\Container\ContainerInterface;
use N3vrax\DkAuthentication\AuthenticationInterface;

class DefaultAuthorizationListenerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new DefaultAuthorizationListener(
            $container->get(GuardsProvider::class),
            $container->get(AuthenticationInterface::class)
        );
    }
}