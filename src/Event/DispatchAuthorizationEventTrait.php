<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
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
