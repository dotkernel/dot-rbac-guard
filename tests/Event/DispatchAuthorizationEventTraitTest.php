<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Event;

use Dot\Rbac\Guard\Event\AuthorizationEvent;
use Dot\Rbac\Guard\Event\DispatchAuthorizationEventTrait;
use PHPUnit\Framework\TestCase;

class DispatchAuthorizationEventTraitTest extends TestCase
{
    use DispatchAuthorizationEventTrait;

    public function testDispatchEvent(): void
    {
        $name = 'name';

        $result = $this->dispatchEvent($name);
        $this->assertInstanceOf(AuthorizationEvent::class, $result);
    }
}
