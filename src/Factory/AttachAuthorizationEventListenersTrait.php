<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Event\AuthorizationEventListenerInterface;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Psr\Container\ContainerInterface;
use Laminas\EventManager\EventManagerInterface;

/**
 * Class AttachAuthorizationEventListenersTrait
 * @package Dot\Rbac\Guard\Factory
 */
trait AttachAuthorizationEventListenersTrait
{
    /**
     * @param ContainerInterface $container
     * @param EventManagerInterface $eventManager
     */
    protected function attachListeners(ContainerInterface $container, EventManagerInterface $eventManager)
    {
        /** @var RbacGuardOptions $options */
        $options = $container->get(RbacGuardOptions::class);

        if (!empty($options->getEventListeners())
            && is_array($options->getEventListeners())
        ) {
            $listeners = $options->getEventListeners();
            foreach ($listeners as $listener) {
                if (is_string($listener)) {
                    $l = $this->getListenerObject($container, $listener);
                    $p = 1;
                    $l->attach($eventManager, $p);
                } elseif (is_array($listener)) {
                    $l = $listener['type'] ?? '';
                    $p = $listener['priority'] ?? 1;

                    $l = $this->getListenerObject($container, $l);
                    $l->attach($eventManager, $p);
                }
            }
        }
    }

    /**
     * @param ContainerInterface $container
     * @param string $listener
     * @return AuthorizationEventListenerInterface
     */
    protected function getListenerObject(
        ContainerInterface $container,
        string $listener
    ): AuthorizationEventListenerInterface {
        if ($container->has($listener)) {
            $listener = $container->get($listener);
        }

        if (is_string($listener) && class_exists($listener)) {
            $listener = new $listener();
        }

        if (!$listener instanceof AuthorizationEventListenerInterface) {
            throw new RuntimeException('Authorization event listener is not an instance of '
                . AuthorizationEventListenerInterface::class);
        }

        return $listener;
    }
}
