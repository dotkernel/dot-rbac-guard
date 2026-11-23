<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Fixture;

use Dot\Rbac\Guard\Factory\AttachAuthorizationEventListenersTrait;
use Laminas\EventManager\EventManagerInterface;
use Psr\Container\ContainerInterface;

/**
 * Makes the protected methods of AttachAuthorizationEventListenersTrait public for the tests.
 */
final class ListenerHost
{
    use AttachAuthorizationEventListenersTrait;

    public function attach(ContainerInterface $container, EventManagerInterface $eventManager): void
    {
        $this->attachListeners($container, $eventManager);
    }
}
