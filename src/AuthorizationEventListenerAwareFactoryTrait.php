<?php
/**
 * @copyright: DotKernel
 * @library: dot-rbac-guard
 * @author: n3vrax
 * Date: 2/1/2017
 * Time: 10:33 PM
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard;

use Dot\Rbac\Guard\Event\AuthorizationEventListenerAwareInterface;
use Dot\Rbac\Guard\Event\AuthorizationEventListenerInterface;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Interop\Container\ContainerInterface;

/**
 * Class AuthorizationEventListenerAwareFactoryTrait
 * @package Dot\Rbac\Guard
 */
trait AuthorizationEventListenerAwareFactoryTrait
{
    /**
     * @param ContainerInterface $container
     * @param AuthorizationEventListenerAwareInterface $handler
     * @param string $eventName
     */
    protected function attachEventListeners(
        ContainerInterface $container,
        AuthorizationEventListenerAwareInterface $handler,
        string $eventName
    ) {
        /** @var RbacGuardOptions $options */
        $options = $container->get(RbacGuardOptions::class);
        $eventListenersConfig = $options->getEventListeners();
        if (isset($eventListenersConfig[$eventName])
            && is_array($eventListenersConfig[$eventName])
        ) {
            foreach ($eventListenersConfig[$eventName] as $listenerConfig) {
                if (is_array($listenerConfig)) {
                    $type = $listenerConfig['type'] ?? '';
                    $priority = $listenerConfig['priority'] ?? -2000;

                    $listener = $this->getListenerObject($container, $type);
                    $handler->attachListener($listener, $priority, $eventName);
                } elseif (is_string($listenerConfig)) {
                    $type = $listenerConfig;
                    $priority = -2000;

                    $listener = $this->getListenerObject($container, $type);
                    $handler->attachListener($listener, $priority, $eventName);
                }
            }
        }
    }

    /**
     * @param ContainerInterface $container
     * @param string $type
     * @return AuthorizationEventListenerInterface
     */
    protected function getListenerObject(
        ContainerInterface $container,
        string $type
    ): AuthorizationEventListenerInterface {
        $listener = $type;
        if ($container->has($listener)) {
            $listener = $container->get($listener);
        }
        if (is_string($listener) && class_exists($listener)) {
            $listener = new $listener();
        }
        if (!$listener instanceof AuthorizationEventListenerInterface) {
            throw new RuntimeException(sprintf(
                'Authorization event listener must be an instance of %s, but %s was given',
                AuthorizationEventListenerInterface::class,
                is_object($listener) ? get_class($listener) : gettype($listener)
            ));
        }
        return $listener;
    }
}
