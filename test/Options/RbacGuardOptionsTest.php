<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Options;

use Dot\Rbac\Guard\Guard\GuardInterface;
use Dot\Rbac\Guard\Options\MessagesOptions;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use PHPUnit\Framework\TestCase;

class RbacGuardOptionsTest extends TestCase
{
    public function testAccessors(): void
    {
        $options = new RbacGuardOptions([
            'protection_policy' => GuardInterface::POLICY_DENY,
            'guards_provider'   => ['type' => 'ArrayGuards'],
            'messages_options'  => ['messages' => [MessagesOptions::FORBIDDEN => 'No']],
            'event_listeners'   => ['listener'],
            'unknown_key'       => 'ignored',
        ]);

        $this->assertSame(GuardInterface::POLICY_DENY, $options->getProtectionPolicy());
        $this->assertSame(['type' => 'ArrayGuards'], $options->getGuardsProvider());
        $this->assertSame('No', $options->getMessagesOptions()->getMessage(MessagesOptions::FORBIDDEN));
        $this->assertSame(['listener'], $options->getEventListeners());
    }

    public function testDefaults(): void
    {
        $options = new RbacGuardOptions(null);

        $this->assertSame(GuardInterface::POLICY_ALLOW, $options->getProtectionPolicy());
        $this->assertSame([], $options->getGuardsProvider());
        $this->assertSame([], $options->getEventListeners());
        $this->assertSame($options->getMessagesOptions(), $options->getMessagesOptions());
        $this->assertNotSame('', $options->getMessagesOptions()->getMessage(MessagesOptions::UNAUTHORIZED));
    }
}
