<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Factory;

use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Middleware\ForbiddenHandler;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function is_array;
use function is_string;

class ForbiddenHandlerFactory
{
    use AttachAuthorizationEventListenersTrait;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName): ForbiddenHandler
    {
        $config = $container->get('config');
        $config = is_array($config) ? $config : [];
        $debug  = ($config['debug'] ?? false) === true;

        $authorizationService = $container->get(AuthorizationInterface::class);
        $moduleOptions        = $container->get(RbacGuardOptions::class);

        $template = $config['mezzio']['error_handler']['template_403'] ?? null;
        if (! is_string($template) || $template === '') {
            $template = ForbiddenHandler::TEMPLATE_DEFAULT;
        }

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
