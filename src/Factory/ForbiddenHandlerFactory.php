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
use Dot\Rbac\Guard\Middleware\ForbiddenHandler;
use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

/**
 * Class ForbiddenHandlerFactory
 * @package Dot\Rbac\Guard\Factory
 */
class ForbiddenHandlerFactory
{
    use AttachAuthorizationEventListenersTrait;

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return ForbiddenHandler
     */
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        $authorizationService = $container->get(AuthorizationInterface::class);
        /** @var ForbiddenHandler $handler */
        $handler = new $requestedName($authorizationService);
        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();

        $handler->setEventManager($eventManager);
        $handler->attach($eventManager, 1000);

        $this->attachListeners($container, $eventManager);

        return $handler;
    }
}
