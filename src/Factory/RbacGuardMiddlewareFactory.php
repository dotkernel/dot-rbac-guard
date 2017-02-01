<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 2:14 AM
 */

declare(strict_types = 1);

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
     * @param $requestedName
     * @return RbacGuardMiddleware
     */
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();

        /** @var DefaultAuthorizationListener $defaultListener */
        $defaultListener = $container->get(DefaultAuthorizationListener::class);
        $defaultListener->attach($eventManager);

        /** @var RbacGuardMiddleware $middleware */
        $middleware = new $requestedName($container->get(AuthorizationInterface::class));
        $middleware->setEventManager($eventManager);

        return $middleware;
    }
}
