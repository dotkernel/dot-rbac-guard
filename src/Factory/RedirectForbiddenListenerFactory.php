<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 6/18/2016
 * Time: 6:15 PM
 */

namespace Dot\Rbac\Guard\Factory;

use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Rbac\Guard\Listener\RedirectForbiddenListener;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Helper\UrlHelper;

/**
 * Class RedirectForbiddenListenerFactory
 * @package Dot\Rbac\Guard\Factory
 */
class RedirectForbiddenListenerFactory
{
    /**
     * @param ContainerInterface $container
     * @return RedirectForbiddenListener
     */
    public function __invoke(ContainerInterface $container)
    {
        return new RedirectForbiddenListener(
            $container->get(UrlHelper::class),
            $container->get(FlashMessengerInterface::class),
            $container->get(RbacGuardOptions::class)
        );
    }
}