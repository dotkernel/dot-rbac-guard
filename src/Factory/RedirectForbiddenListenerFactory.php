<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 6/18/2016
 * Time: 6:15 PM
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Factory;

use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Helpers\Route\RouteOptionHelper;
use Dot\Rbac\Guard\Listener\RedirectForbiddenListener;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Interop\Container\ContainerInterface;

/**
 * Class RedirectForbiddenListenerFactory
 * @package Dot\Rbac\Guard\Factory
 */
class RedirectForbiddenListenerFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return RedirectForbiddenListener
     */
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        $config = $container->get('config');
        $debug = $config['debug'] ?? false;

        $flashMessenger = $container->has(FlashMessengerInterface::class)
            ? $container->get(FlashMessengerInterface::class)
            : null;

        /** @var RedirectForbiddenListener $listener */
        $listener = new $requestedName(
            $container->get(RouteOptionHelper::class),
            $container->get(RbacGuardOptions::class),
            $flashMessenger
        );
        $listener->setDebug($debug);

        return $listener;
    }
}
