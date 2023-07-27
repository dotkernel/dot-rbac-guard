<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Options;

use Dot\Rbac\Guard\Options\MessagesOptions;
use PHPUnit\Framework\TestCase;

class MessagesOptionsTest extends TestCase
{
    protected MessagesOptions $subject;

    public function setUp(): void
    {
        $this->subject = new MessagesOptions();
    }

    public function testGetMessages(): void
    {
        $this->subject->setMessages(['testMessage']);
        $this->assertCount(2, $this->subject->getMessages());
    }

    public function testGetMessage(): void
    {
        $this->assertEmpty($this->subject->getMessage(1000));
        $this->assertIsString($this->subject->getMessage(MessagesOptions::UNAUTHORIZED));
    }
}
