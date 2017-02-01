<?php
/**
 * @copyright: DotKernel
 * @library: dot-rbac-guard
 * @author: n3vrax
 * Date: 2/1/2017
 * Time: 10:04 PM
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Event;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * Interface AuthorizationEventListenerInterface
 * @package Dot\Rbac\Guard\Event
 */
interface AuthorizationEventListenerInterface extends ListenerAggregateInterface
{
    /**
     * @param EventManagerInterface $events
     * @param int $priority
     * @param string $eventName
     */
    public function attach(EventManagerInterface $events, $priority = 1, string $eventName = '');

    /**
     * @param AuthorizationEvent $e
     */
    public function onAuthorize(AuthorizationEvent $e);

    /**
     * @param AuthorizationEvent $e
     */
    public function onForbidden(AuthorizationEvent $e);
}
