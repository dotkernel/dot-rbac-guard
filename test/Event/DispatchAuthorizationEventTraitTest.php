<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Event;

use Dot\Rbac\Guard\Event\AuthorizationEvent;
use DotTest\Rbac\Guard\Fixture\EventDispatcher;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\RedirectResponse;
use PHPUnit\Framework\TestCase;
use stdClass;

class DispatchAuthorizationEventTraitTest extends TestCase
{
    public function testWithoutListenersReturnsEvent(): void
    {
        $dispatcher = new EventDispatcher();
        $result     = $dispatcher->dispatchEvent('name', ['a' => 1]);

        $this->assertInstanceOf(AuthorizationEvent::class, $result);
        $this->assertSame('name', $result->getName());
        $this->assertSame($dispatcher, $result->getTarget());
        $this->assertSame(['a' => 1], $result->getParams());
    }

    public function testUsesGivenTarget(): void
    {
        $target = new stdClass();
        $result = (new EventDispatcher())->dispatchEvent('name', [], $target);

        $this->assertInstanceOf(AuthorizationEvent::class, $result);
        $this->assertSame($target, $result->getTarget());
    }

    public function testListenerThatReturnsResponseStopsTheEvent(): void
    {
        $response   = new RedirectResponse('/login');
        $called     = false;
        $dispatcher = new EventDispatcher();
        $dispatcher->getEventManager()->attach('name', static fn (): RedirectResponse => $response, 10);
        $dispatcher->getEventManager()->attach('name', static function () use (&$called): void {
            $called = true;
        }, 1);

        $this->assertSame($response, $dispatcher->dispatchEvent('name'));
        $this->assertFalse($called);
    }

    public function testListenerThatReturnsOtherValueDoesNotStopTheEvent(): void
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->getEventManager()->attach('name', static fn (): string => 'not a response', 10);
        $dispatcher->getEventManager()->attach('name', static fn (): Response => new Response(), 1);

        $this->assertInstanceOf(Response::class, $dispatcher->dispatchEvent('name'));

        $dispatcher = new EventDispatcher();
        $dispatcher->getEventManager()->attach('name', static fn (): string => 'not a response');

        $this->assertInstanceOf(AuthorizationEvent::class, $dispatcher->dispatchEvent('name'));
    }
}
