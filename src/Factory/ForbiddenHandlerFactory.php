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
        $handler->attach($handler->getEventManager(), 1000);

        $this->attachListeners($container, $handler->getEventManager());

        return $handler;
    }
}
