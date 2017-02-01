<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/24/2016
 * Time: 3:45 PM
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Factory;

use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\AuthorizationEventListenerAwareFactoryTrait;
use Dot\Rbac\Guard\Event\AuthorizationEvent;
use Dot\Rbac\Guard\Listener\RedirectForbiddenListener;
use Dot\Rbac\Guard\Middleware\ForbiddenHandler;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

/**
 * Class ForbiddenHandlerFactory
 * @package Dot\Rbac\Guard\Factory
 */
class ForbiddenHandlerFactory
{
    use AuthorizationEventListenerAwareFactoryTrait;

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return ForbiddenHandler
     */
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        /** @var RbacGuardOptions $options */
        $options = $container->get(RbacGuardOptions::class);
        $authorizationService = $container->get(AuthorizationInterface::class);

        /** @var ForbiddenHandler $handler */
        $handler = new $requestedName($authorizationService);
        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();

        if ($options->getRedirectOptions()->isEnable()) {
            $listener = $container->get(RedirectForbiddenListener::class);
            $eventManager->attach(AuthorizationEvent::EVENT_FORBIDDEN, $listener, 1);
        }

        $handler->setEventManager($eventManager);
        $this->attachEventListeners($container, $handler, AuthorizationEvent::EVENT_FORBIDDEN);

        return $handler;
    }
}
