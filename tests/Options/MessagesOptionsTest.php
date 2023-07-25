<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Options;

use Dot\Rbac\Guard\Options\MessagesOptions;
use PHPUnit\Framework\TestCase;

class MessagesOptionsTest extends TestCase
{
    protected MessagesOptions $subject;
    protected array $messageArray = ['testMessage'];
    protected int $messageKey     = 0;

    public function setUp(): void
    {
        $this->subject = new MessagesOptions();
    }

    public function testGetMessages()
    {
        $this->subject->setMessages($this->messageArray);
        $result = $this->subject->getMessages();

        $this->assertIsArray($result);
    }

    public function testGetMessage()
    {
        $this->subject->setMessages($this->messageArray);
        $result = $this->subject->getMessage($this->messageKey);

        $this->assertIsString($result);
    }
}
