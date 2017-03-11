<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Event;

use Dot\Event\Event;

/**
 * Class AuthorizationEvent
 * @package Dot\Rbac\Guard\Event
 */
class AuthorizationEvent extends Event
{
    const EVENT_BEFORE_AUTHORIZATION = 'event.beforeAuthorization';
    const EVENT_AFTER_AUTHORIZATION = 'event.afterAuthorization';

    const EVENT_FORBIDDEN = 'event.authorization.forbidden';
}
