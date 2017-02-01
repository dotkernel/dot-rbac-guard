<?php
/**
 * @copyright: DotKernel
 * @library: dot-rbac-guard
 * @author: n3vrax
 * Date: 2/1/2017
 * Time: 10:13 PM
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Event;

/**
 * Interface AuthorizationEventListenerAwareInterface
 * @package Dot\Rbac\Guard\Event
 */
interface AuthorizationEventListenerAwareInterface
{
    /**
     * @param AuthorizationEventListenerInterface $listener
     * @param int $priority
     * @param string $eventName
     */
    public function attachListener(
        AuthorizationEventListenerInterface $listener,
        $priority = 1,
        string $eventName = ''
    );

    /**
     * @param AuthorizationEventListenerInterface $listener
     */
    public function detachListener(AuthorizationEventListenerInterface $listener);

    /**
     * Detach and clears all listeners
     */
    public function clearListeners();
}
