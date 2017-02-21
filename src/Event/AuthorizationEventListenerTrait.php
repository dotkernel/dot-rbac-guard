<?php
/**
 * @copyright: DotKernel
 * @library: dot-rbac-guard
 * @author: n3vrax
 * Date: 2/22/2017
 * Time: 12:21 AM
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Event;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateTrait;

/**
 * Class AuthorizationEventListenerTrait
 * @package Dot\Rbac\Guard\Event
 */
trait AuthorizationEventListenerTrait
{
    use ListenerAggregateTrait;

    public function attach(EventManagerInterface $events, $priority = 1)
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
     * @param AuthorizationEvent $e
     */
    public function onBeforeAuthorization(AuthorizationEvent $e)
    {
        // no-op
    }

    /**
     * @param AuthorizationEvent $e
     */
    public function onAfterAuthorization(AuthorizationEvent $e)
    {
        // no-op
    }

    /**
     * @param AuthorizationEvent $e
     */
    public function onForbidden(AuthorizationEvent $e)
    {
        // no-op
    }
}
