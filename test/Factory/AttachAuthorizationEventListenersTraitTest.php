<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Factory;

use ArrayObject;
use Dot\Rbac\Guard\Event\AuthorizationEvent;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use DotTest\Rbac\Guard\Fixture\AuthorizationListener;
use DotTest\Rbac\Guard\Fixture\InMemoryContainer;
use DotTest\Rbac\Guard\Fixture\ListenerHost;
use Laminas\EventManager\EventManager;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class AttachAuthorizationEventListenersTraitTest extends TestCase
{
    /**
     * @param array<array-key, mixed> $listeners
     */
    private function container(array $listeners): InMemoryContainer
    {
        return new InMemoryContainer([
            RbacGuardOptions::class => new RbacGuardOptions(['event_listeners' => $listeners]),
        ]);
    }

    public function testNoListeners(): void
    {
        $eventManager = new EventManager();
        (new ListenerHost())->attach($this->container([]), $eventManager);

        $this->assertCount(0, $eventManager->triggerEvent(new AuthorizationEvent(AuthorizationEvent::EVENT_FORBIDDEN)));
    }

    public function testAttachesServiceFromContainer(): void
    {
        $listener  = new AuthorizationListener();
        $container = $this->container(['listener.service']);
        $container->set('listener.service', $listener);

        $eventManager = new EventManager();
        (new ListenerHost())->attach($container, $eventManager);
        $eventManager->triggerEvent(new AuthorizationEvent(AuthorizationEvent::EVENT_FORBIDDEN));

        $this->assertSame([AuthorizationEvent::EVENT_FORBIDDEN], $listener->received);
    }

    public function testAttachesClassName(): void
    {
        $eventManager = new EventManager();
        (new ListenerHost())->attach($this->container([AuthorizationListener::class]), $eventManager);

        $this->assertCount(1, $eventManager->triggerEvent(new AuthorizationEvent(AuthorizationEvent::EVENT_FORBIDDEN)));
    }

    public function testAttachesWithPriority(): void
    {
        /** @var ArrayObject<int, string> $log */
        $log       = new ArrayObject();
        $container = $this->container([['type' => 'listener.service', 'priority' => 50]]);
        $container->set('listener.service', new AuthorizationListener($log));

        $eventManager = new EventManager();
        (new ListenerHost())->attach($container, $eventManager);
        $eventManager->attach(AuthorizationEvent::EVENT_BEFORE_AUTHORIZATION, static function () use ($log): void {
            $log->append('priority 10');
        }, 10);
        $eventManager->attach(AuthorizationEvent::EVENT_BEFORE_AUTHORIZATION, static function () use ($log): void {
            $log->append('priority 100');
        }, 100);
        $eventManager->triggerEvent(new AuthorizationEvent(AuthorizationEvent::EVENT_BEFORE_AUTHORIZATION));

        $this->assertSame(['priority 100', 'listener', 'priority 10'], $log->getArrayCopy());
    }

    /**
     * @return array<string, array{array<array-key, mixed>}>
     */
    public static function invalidListenersProvider(): array
    {
        return [
            'unknown class name'   => [['No\\Such\\Listener']],
            'not a listener'       => [[stdClass::class]],
            'array without a type' => [[['priority' => 1]]],
        ];
    }

    /**
     * @param array<array-key, mixed> $listeners
     */
    #[DataProvider('invalidListenersProvider')]
    public function testInvalidListenerThrows(array $listeners): void
    {
        $this->expectException(RuntimeException::class);

        (new ListenerHost())->attach($this->container($listeners), new EventManager());
    }
}
