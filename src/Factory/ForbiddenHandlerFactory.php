<?php

/**
 * see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Factory;

use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Middleware\ForbiddenHandler;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function is_bool;

class ForbiddenHandlerFactory
{
    use AttachAuthorizationEventListenersTrait;

    /**
     * @return ForbiddenHandler
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName)
    {
        $config = $container->get('config');
        $debug  = is_bool($config['debug']) && $config['debug'];

        $authorizationService = $container->get(AuthorizationInterface::class);
        $moduleOptions        = $container->get(RbacGuardOptions::class);

        $template = $config['mezzio']['error_handler']['template_403'] ?? ForbiddenHandler::TEMPLATE_DEFAULT;

        $renderer = $container->has(TemplateRendererInterface::class)
            ? $container->get(TemplateRendererInterface::class)
            : null;

        /** @var ForbiddenHandler $handler */
        $handler = new $requestedName($authorizationService, $moduleOptions, $renderer, $template);
        $handler->setDebug($debug);
        $handler->attach($handler->getEventManager(), 1000);

        $this->attachListeners($container, $handler->getEventManager());

        return $handler;
    }
}
