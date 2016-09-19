<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/18/2016
 * Time: 6:15 PM
 */

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Listener\RedirectForbiddenListener;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Interop\Container\ContainerInterface;
use N3vrax\DkSession\FlashMessenger\FlashMessengerInterface;
use Zend\Expressive\Helper\UrlHelper;

class RedirectForbiddenListenerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new RedirectForbiddenListener(
            $container->get(UrlHelper::class),
            $container->get(FlashMessengerInterface::class),
            $container->get(RbacGuardOptions::class)
        );
    }
}