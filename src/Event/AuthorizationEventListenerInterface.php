<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Event;

use Laminas\EventManager\ListenerAggregateInterface;

interface AuthorizationEventListenerInterface extends ListenerAggregateInterface
{
    /**
     * @param AuthorizationEvent<*, *> $e
     */
    public function onBeforeAuthorization(AuthorizationEvent $e): void;

    /**
     * @param AuthorizationEvent<*, *> $e
     */
    public function onAfterAuthorization(AuthorizationEvent $e): void;

    /**
     * @param AuthorizationEvent<*, *> $e
     */
    public function onForbidden(AuthorizationEvent $e): void;
}
