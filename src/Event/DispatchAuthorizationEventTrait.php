<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Event;

use Laminas\EventManager\EventManagerAwareTrait;
use Psr\Http\Message\ResponseInterface;

trait DispatchAuthorizationEventTrait
{
    use EventManagerAwareTrait;

    /**
     * Trigger an authorization event.
     *
     * Return the response of the first listener that returns a ResponseInterface, or the event otherwise.
     *
     * @param array<string, mixed> $params
     * @return AuthorizationEvent<object|string, array<string, mixed>>|ResponseInterface
     */
    public function dispatchEvent(
        string $name,
        array $params = [],
        object|string|null $target = null
    ): AuthorizationEvent|ResponseInterface {
        $event  = new AuthorizationEvent($name, $target ?? $this, $params);
        $result = $this->getEventManager()->triggerEventUntil(
            static fn (mixed $response): bool => $response instanceof ResponseInterface,
            $event
        );

        $last = $result->last();
        if ($result->stopped() && $last instanceof ResponseInterface) {
            return $last;
        }

        return $event;
    }
}
