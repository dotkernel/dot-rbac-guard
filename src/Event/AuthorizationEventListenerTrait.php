<?php

/**
 * see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Event;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateTrait;

trait AuthorizationEventListenerTrait
{
    use ListenerAggregateTrait;

    public function attach(EventManagerInterface $events, mixed $priority = 1)
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

    public function onBeforeAuthorization(AuthorizationEvent $e)
    {
        // no-op
    }

    public function onAfterAuthorization(AuthorizationEvent $e)
    {
        // no-op
    }

    public function onForbidden(AuthorizationEvent $e)
    {
        // no-op
    }
}
