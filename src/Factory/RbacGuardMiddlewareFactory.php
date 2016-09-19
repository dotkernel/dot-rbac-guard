<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 5/21/2016
 * Time: 2:14 AM
 */

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Event\AuthorizationEvent;
use Dot\Rbac\Guard\Listener\DefaultAuthorizationListener;
use Dot\Rbac\Guard\Middleware\RbacGuardMiddleware;
use Interop\Container\ContainerInterface;
use N3vrax\DkAuthorization\AuthorizationInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

class RbacGuardMiddlewareFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $event = new AuthorizationEvent();
        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();

        $event->setAuthorizationService($container->get(AuthorizationInterface::class));

        /** @var DefaultAuthorizationListener $defaultListener */
        $defaultListener = $container->get(DefaultAuthorizationListener::class);
        $defaultListener->attach($eventManager);

        $middleware = new RbacGuardMiddleware();
        $middleware->setEvent($event);
        $middleware->setEventManager($eventManager);

        return $middleware;
    }
}