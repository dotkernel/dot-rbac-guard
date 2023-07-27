<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Options;

use Dot\Rbac\Guard\Options\MessagesOptions;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use PHPUnit\Framework\TestCase;

class RbacGuardOptionsTest extends TestCase
{
    protected RbacGuardOptions $subject;

    public function setUp(): void
    {
        $this->subject = new RbacGuardOptions(
            [
                'protection_policy' => 'ProtectionPolicy',
                'guards_provider'   => [
                    'some' => 'providers',
                ],
                'messages_options'  => [
                    'messages' => [
                        'option2',
                    ],
                ],
                'event_listeners'   => [
                    'event' => 'listener',
                ],
            ],
        );
    }

    public function testAccessors(): void
    {
        $this->assertIsString($this->subject->getProtectionPolicy());
        $this->assertIsArray($this->subject->getGuardsProvider());
        $this->assertInstanceOf(MessagesOptions::class, $this->subject->getMessagesOptions());
        $this->assertIsArray($this->subject->getEventListeners());
    }
}
