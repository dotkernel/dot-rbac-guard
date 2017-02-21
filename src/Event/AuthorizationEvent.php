<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 6/14/2016
 * Time: 5:27 PM
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
