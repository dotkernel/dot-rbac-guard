<?php

/**
 * see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Event;

use Laminas\EventManager\EventManagerAwareTrait;
use Laminas\EventManager\ResponseCollection;
use Psr\Http\Message\ResponseInterface;

trait DispatchAuthorizationEventTrait
{
    use EventManagerAwareTrait;

    /**
     * @return AuthorizationEvent|ResponseCollection
     */
    public function dispatchEvent(string $name, array $params = [], mixed $target = null)
    {
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
