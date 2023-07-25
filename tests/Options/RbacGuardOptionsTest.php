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
        $this->subject = new RbacGuardOptions(null);
    }

    public function testGetProtectionPolicy()
    {
        $this->subject->setProtectionPolicy('ProtectionPolicy');
        $result = $this->subject->getProtectionPolicy();

        $this->assertIsString($result);
    }

    public function testGetGuardsProvider()
    {
        $this->subject->setGuardsProvider(['some' => 'providers']);
        $result = $this->subject->getGuardsProvider();

        $this->assertIsArray($result);
    }

    public function testGetMessagesOptions()
    {
        $this->subject->setMessagesOptions(['protectionPolicy' => 'option']);
        $result = $this->subject->getMessagesOptions();

        $this->assertInstanceOf(MessagesOptions::class, $result);
    }

    public function testGetEventListeners()
    {
        $this->subject->setEventListeners(['event' => 'listener']);
        $result = $this->subject->getEventListeners();

        $this->assertIsArray($result);
    }
}
