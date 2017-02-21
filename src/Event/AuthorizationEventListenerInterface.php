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
