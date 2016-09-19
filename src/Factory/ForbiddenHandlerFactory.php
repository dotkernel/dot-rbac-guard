<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 5/24/2016
 * Time: 3:45 PM
 */

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Event\AuthorizationEvent;
use Dot\Rbac\Guard\Listener\RedirectForbiddenListener;
use Dot\Rbac\Guard\Middleware\ForbiddenHandler;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Interop\Container\ContainerInterface;
use N3vrax\DkAuthorization\AuthorizationInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

class ForbiddenHandlerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var RbacGuardOptions $options */
        $options = $container->get(RbacGuardOptions::class);

        $handler = new ForbiddenHandler();
        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();

        if($options->isEnableRedirectForbiddenListener()) {
            $listener = $container->get(RedirectForbiddenListener::class);
            $eventManager->attach(AuthorizationEvent::EVENT_FORBIDDEN, $listener, 1);
        }

        $event = new AuthorizationEvent();
        $event->setAuthorizationService($container->get(AuthorizationInterface::class));

        $handler->setEvent($event);
        $handler->setEventManager($eventManager);
        return $handler;
    }
}