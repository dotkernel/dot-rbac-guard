<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Event;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateTrait;

trait AuthorizationEventListenerTrait
{
    use ListenerAggregateTrait;

    public function attach(EventManagerInterface $events, mixed $priority = 1): void
    {
        $this->listeners[] = $events->attach(
            AuthorizationEvent::EVENT_BEFORE_AUTHORIZATION,
            [$this, 'onBeforeAuthorization'],
            $priority
        );
        $this->listeners[] = $events->attach(
            AuthorizationEvent::EVENT_AFTER_AUTHORIZATION,
            [$this, 'onAfterAuthorization'],
            $priority
        );
        $this->listeners[] = $events->attach(
            AuthorizationEvent::EVENT_FORBIDDEN,
            [$this, 'onForbidden'],
            $priority
        );
    }

    /**
     * @param AuthorizationEvent<*, *> $e
     */
    public function onBeforeAuthorization(AuthorizationEvent $e): void
    {
    }

    /**
     * @param AuthorizationEvent<*, *> $e
     */
    public function onAfterAuthorization(AuthorizationEvent $e): void
    {
    }

    /**
     * @param AuthorizationEvent<*, *> $e
     */
    public function onForbidden(AuthorizationEvent $e): void
    {
    }
}
