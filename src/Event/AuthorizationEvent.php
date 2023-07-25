<?php

/**
 * see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Event;

use Dot\Event\Event;

class AuthorizationEvent extends Event
{
    public const EVENT_BEFORE_AUTHORIZATION = 'event.beforeAuthorization';
    public const EVENT_AFTER_AUTHORIZATION  = 'event.afterAuthorization';

    public const EVENT_FORBIDDEN = 'event.authorization.forbidden';
}
