<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 2:14 AM
 */

namespace Dot\Rbac\Guard\Factory;

use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Listener\DefaultAuthorizationListener;
use Dot\Rbac\Guard\Middleware\RbacGuardMiddleware;
use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

/**
 * Class RbacGuardMiddlewareFactory
 * @package Dot\Rbac\Guard\Factory
 */
class RbacGuardMiddlewareFactory
{
    /**
     * @param ContainerInterface $container
     * @return RbacGuardMiddleware
     */
    public function __invoke(ContainerInterface $container)
    {
        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();

        /** @var DefaultAuthorizationListener $defaultListener */
        $defaultListener = $container->get(DefaultAuthorizationListener::class);
        $defaultListener->attach($eventManager);

        $middleware = new RbacGuardMiddleware($container->get(AuthorizationInterface::class));
        $middleware->setEventManager($eventManager);

        return $middleware;
    }
}