<?php
/**
 * @copyright: DotKernel
 * @library: dot-rbac-guard
 * @author: n3vrax
 * Date: 2/22/2017
 * Time: 12:26 AM
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Event;

use Psr\Http\Message\ResponseInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\ResponseCollection;

/**
 * Class DispatchAuthorizationEventTrait
 * @package Dot\Rbac\Guard\Event
 */
trait DispatchAuthorizationEventTrait
{
    use EventManagerAwareTrait;

    /**
     * @param string $name
     * @param array $params
     * @param null $target
     * @return AuthorizationEvent|ResponseCollection
     */
    public function dispatchEvent(string $name, array $params = [], $target = null)
    {
        if ($target === null) {
            $target = $this;
        }

        $event = new AuthorizationEvent($name, $target, $params);
        $r = $this->getEventManager()->triggerEventUntil(function ($r) {
            return ($r instanceof ResponseInterface);
        }, $event);

        if ($r->stopped()) {
            return $r->last();
        }

        return $event;
    }
}
