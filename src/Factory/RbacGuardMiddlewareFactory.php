<?php

/**
 * see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Factory;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Middleware\RbacGuardMiddleware;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Guard\Provider\Factory;
use Dot\Rbac\Guard\Provider\GuardsProviderPluginManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class RbacGuardMiddlewareFactory
{
    use AttachAuthorizationEventListenersTrait;

    /**
     * @return RbacGuardMiddleware
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName)
    {
        /** @var RbacGuardOptions $options */
        $options = $container->get(RbacGuardOptions::class);

        /** @var Factory $guardsProviderFactory */
        $guardsProviderFactory = new Factory($container, $container->get(GuardsProviderPluginManager::class));
        $guardsProvider        = $guardsProviderFactory->create($options->getGuardsProvider());

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
