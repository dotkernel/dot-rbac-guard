<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Event;

use Laminas\EventManager\ListenerAggregateInterface;

interface AuthorizationEventListenerInterface extends ListenerAggregateInterface
{
    public function onBeforeAuthorization(AuthorizationEvent $e): void;

    public function onAfterAuthorization(AuthorizationEvent $e): void;

    public function onForbidden(AuthorizationEvent $e): void;
}
