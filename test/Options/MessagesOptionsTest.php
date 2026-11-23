<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Options;

use Dot\Rbac\Guard\Options\MessagesOptions;
use PHPUnit\Framework\TestCase;

class MessagesOptionsTest extends TestCase
{
    public function testSetMessagesReplacesByKey(): void
    {
        $options = new MessagesOptions();
        $options->setMessages([MessagesOptions::UNAUTHORIZED => 'Sign in']);

        $this->assertCount(2, $options->getMessages());
        $this->assertSame('Sign in', $options->getMessage(MessagesOptions::UNAUTHORIZED));
        $this->assertNotSame('', $options->getMessage(MessagesOptions::FORBIDDEN));
    }

    public function testUnknownKeyReturnsEmptyString(): void
    {
        $this->assertSame('', (new MessagesOptions())->getMessage(1000));
    }
}
