<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Event;

use Zend\EventManager\ListenerAggregateInterface;

/**
 * Interface AuthorizationEventListenerInterface
 * @package Dot\Rbac\Guard\Event
 */
interface AuthorizationEventListenerInterface extends ListenerAggregateInterface
{
    /**
     * @param AuthorizationEvent $e
     */
    public function onBeforeAuthorization(AuthorizationEvent $e);

    /**
     * @param AuthorizationEvent $e
     */
    public function onAfterAuthorization(AuthorizationEvent $e);

    /**
     * @param AuthorizationEvent $e
     */
    public function onForbidden(AuthorizationEvent $e);
}
