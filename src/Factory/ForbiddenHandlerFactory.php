<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Factory;

use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Middleware\ForbiddenHandler;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

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
        $config = $container->get('config');
        $debug = (bool)$config['debug'] ?? false;

        $authorizationService = $container->get(AuthorizationInterface::class);
        $moduleOptions = $container->get(RbacGuardOptions::class);

        $template = isset($config['zend-expressive']['error_handler']['template_403'])
            ? $config['zend-expressive']['error_handler']['template_403']
            : ForbiddenHandler::TEMPLATE_DEFAULT;

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
