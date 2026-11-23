<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Fixture;

use ArrayObject;
use Dot\Rbac\Guard\Event\AbstractAuthorizationEventListener;
use Dot\Rbac\Guard\Event\AuthorizationEvent;

/**
 * Records the names of the events it received.
 */
final class AuthorizationListener extends AbstractAuthorizationEventListener
{
    /** @var list<string> */
    public array $received = [];

    /**
     * @param ArrayObject<int, string>|null $log a log shared with other listeners, to check the order of the calls
     */
    public function __construct(private readonly ?ArrayObject $log = null)
    {
    }

    private function record(string $name): void
    {
        $this->received[] = $name;
        $this->log?->append('listener');
    }

    /**
     * @param AuthorizationEvent<*, *> $e
     */
    public function onBeforeAuthorization(AuthorizationEvent $e): void
    {
        $this->record((string) $e->getName());
    }

    /**
     * @param AuthorizationEvent<*, *> $e
     */
    public function onAfterAuthorization(AuthorizationEvent $e): void
    {
        $this->record((string) $e->getName());
    }

    /**
     * @param AuthorizationEvent<*, *> $e
     */
    public function onForbidden(AuthorizationEvent $e): void
    {
        $this->record((string) $e->getName());
    }
}
