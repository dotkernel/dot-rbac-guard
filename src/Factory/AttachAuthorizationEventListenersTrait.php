<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Event\AuthorizationEventListenerInterface;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Laminas\EventManager\EventManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function class_exists;
use function is_array;
use function is_string;

trait AttachAuthorizationEventListenersTrait
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function attachListeners(ContainerInterface $container, EventManagerInterface $eventManager): void
    {
        /** @var RbacGuardOptions $options */
        $options = $container->get(RbacGuardOptions::class);

        if (! empty($options->getEventListeners())) {
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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

        if (! $listener instanceof AuthorizationEventListenerInterface) {
            throw new RuntimeException('Authorization event listener is not an instance of '
                . AuthorizationEventListenerInterface::class);
        }

        return $listener;
    }
}
