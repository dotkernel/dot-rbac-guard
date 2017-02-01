<?php
/**
 * @copyright: DotKernel
 * @library: dot-rbac-guard
 * @author: n3vrax
 * Date: 2/1/2017
 * Time: 10:14 PM
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Event;

use Zend\EventManager\EventManagerAwareTrait;

/**
 * Class AuthorizationEventListenerAwareTrait
 * @package Dot\Rbac\Guard\Event
 */
trait AuthorizationEventListenerAwareTrait
{
    use EventManagerAwareTrait;

    /** @var AuthorizationEventListenerInterface[] */
    protected $listeners = [];

    /**
     * @param AuthorizationEventListenerInterface $listener
     * @param int $priority
     * @param string $eventName
     */
    public function attachListener(
        AuthorizationEventListenerInterface $listener,
        $priority = 1,
        string $eventName = ''
    ) {
        $listener->attach($this->getEventManager(), $priority, $eventName);
        $this->listeners[] = $listener;
    }

    /**
     * @param AuthorizationEventListenerInterface $listener
     */
    public function detachListener(AuthorizationEventListenerInterface $listener)
    {
        $listener->detach($this->getEventManager());
        $idx = 0;
        foreach ($this->listeners as $l) {
            if ($l === $listener) {
                break;
            }
            $idx++;
        }
        unset($this->listeners[$idx]);
    }

    /**
     * Clears all listeners
     */
    public function clearListeners()
    {
        foreach ($this->listeners as $listener) {
            $listener->detach($this->getEventManager());
        }
        $this->listeners = [];
    }
}
