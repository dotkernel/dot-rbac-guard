<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Event;

use Dot\Rbac\Guard\Event\AbstractAuthorizationEventListener;
use Dot\Rbac\Guard\Event\AuthorizationEvent;
use DotTest\Rbac\Guard\Fixture\AuthorizationListener;
use Laminas\EventManager\EventManager;
use PHPUnit\Framework\TestCase;

class AbstractAuthorizationEventListenerTest extends TestCase
{
    public function testAttachAndDetach(): void
    {
        $listener     = new AuthorizationListener();
        $eventManager = new EventManager();
        $listener->attach($eventManager);

        foreach (
            [
                AuthorizationEvent::EVENT_BEFORE_AUTHORIZATION,
                AuthorizationEvent::EVENT_AFTER_AUTHORIZATION,
                AuthorizationEvent::EVENT_FORBIDDEN,
            ] as $name
        ) {
            $eventManager->triggerEvent(new AuthorizationEvent($name));
        }

        $this->assertSame([
            AuthorizationEvent::EVENT_BEFORE_AUTHORIZATION,
            AuthorizationEvent::EVENT_AFTER_AUTHORIZATION,
            AuthorizationEvent::EVENT_FORBIDDEN,
        ], $listener->received);

        $listener->detach($eventManager);
        $eventManager->triggerEvent(new AuthorizationEvent(AuthorizationEvent::EVENT_FORBIDDEN));
        $this->assertCount(3, $listener->received);
    }

    public function testDefaultHandlersDoNothing(): void
    {
        $listener = new class extends AbstractAuthorizationEventListener {
        };
        $event    = new AuthorizationEvent(AuthorizationEvent::EVENT_FORBIDDEN, $this, ['a' => 1]);

        $listener->onBeforeAuthorization($event);
        $listener->onAfterAuthorization($event);
        $listener->onForbidden($event);

        $this->assertSame(['a' => 1], $event->getParams());
    }
}
