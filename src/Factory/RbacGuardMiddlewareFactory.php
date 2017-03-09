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

use Dot\Authentication\AuthenticationInterface;
use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Middleware\RbacGuardMiddleware;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Guard\Provider\Factory;
use Dot\Rbac\Guard\Provider\GuardsProviderPluginManager;
use Interop\Container\ContainerInterface;

/**
 * Class RbacGuardMiddlewareFactory
 * @package Dot\Rbac\Guard\Factory
 */
class RbacGuardMiddlewareFactory
{
    use AttachAuthorizationEventListenersTrait;

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return RbacGuardMiddleware
     */
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        /** @var RbacGuardOptions $options */
        $options = $container->get(RbacGuardOptions::class);

        /** @var Factory $guardsProviderFactory */
        $guardsProviderFactory = new Factory($container, $container->get(GuardsProviderPluginManager::class));
        $guardsProvider = $guardsProviderFactory->create($options->getGuardsProvider());

        /** @var RbacGuardMiddleware $middleware */
        $middleware = new $requestedName(
            $container->get(AuthorizationInterface::class),
            $guardsProvider,
            $options,
            $container->has(AuthenticationInterface::class)
                ? $container->get(AuthenticationInterface::class)
                : null
        );

        $middleware->attach($middleware->getEventManager(), 1000);
        $this->attachListeners($container, $middleware->getEventManager());

        return $middleware;
    }
}
