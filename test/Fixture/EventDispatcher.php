<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Fixture;

use Dot\Rbac\Guard\Event\DispatchAuthorizationEventTrait;

/**
 * A class that uses DispatchAuthorizationEventTrait.
 */
final class EventDispatcher
{
    use DispatchAuthorizationEventTrait;
}
