<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Event;

use ArrayAccess;
use Dot\Event\Event;

/**
 * @template TTarget of object|string|null
 * @template TParams of array|ArrayAccess|object
 * @extends Event<TTarget, TParams>
 */
class AuthorizationEvent extends Event
{
    public const EVENT_BEFORE_AUTHORIZATION = 'event.beforeAuthorization';
    public const EVENT_AFTER_AUTHORIZATION  = 'event.afterAuthorization';

    public const EVENT_FORBIDDEN = 'event.authorization.forbidden';
}
