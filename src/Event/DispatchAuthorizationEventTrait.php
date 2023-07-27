<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Event;

use Laminas\EventManager\EventManagerAwareTrait;
use Laminas\EventManager\ResponseCollection;
use Psr\Http\Message\ResponseInterface;

trait DispatchAuthorizationEventTrait
{
    use EventManagerAwareTrait;

    public function dispatchEvent(
        string $name,
        array $params = [],
        mixed $target = null
    ): AuthorizationEvent|ResponseCollection {
        if ($target === null) {
            $target = $this;
        }

        $event = new AuthorizationEvent($name, $target, $params);
        $r     = $this->getEventManager()->triggerEventUntil(function ($r) {
            return $r instanceof ResponseInterface;
        }, $event);

        if ($r->stopped()) {
            return $r->last();
        }

        return $event;
    }
}
