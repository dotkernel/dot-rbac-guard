<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Factory\GuardPluginManagerFactory;
use Dot\Rbac\Guard\Factory\GuardsProviderPluginManagerFactory;
use Dot\Rbac\Guard\Factory\RbacGuardOptionsFactory;
use Dot\Rbac\Guard\Guard\GuardInterface;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use DotTest\Rbac\Guard\Fixture\Guard;
use DotTest\Rbac\Guard\Fixture\InMemoryContainer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PluginManagerFactoriesTest extends TestCase
{
    /**
     * @return array<string, array{mixed}>
     */
    public static function incompleteConfigProvider(): array
    {
        return [
            'no dot_authorization key' => [[]],
            'no manager keys'          => [['dot_authorization' => []]],
            'manager keys not arrays'  => [
                ['dot_authorization' => ['guard_manager' => 'x', 'guards_provider_manager' => 'x']],
            ],
        ];
    }

    #[DataProvider('incompleteConfigProvider')]
    public function testManagersAcceptIncompleteConfig(mixed $config): void
    {
        $container = new InMemoryContainer(['config' => $config]);

        $this->assertTrue((new GuardPluginManagerFactory())($container)->has('Route'));
        $this->assertTrue((new GuardsProviderPluginManagerFactory())($container)->has('ArrayGuards'));
    }

    public function testGuardPluginManagerUsesConfig(): void
    {
        $guard     = new Guard(true);
        $container = new InMemoryContainer([
            'config' => [
                'dot_authorization' => [
                    'guard_manager' => ['factories' => ['custom' => static fn (): Guard => $guard]],
                ],
            ],
        ]);

        $this->assertSame($guard, (new GuardPluginManagerFactory())($container)->get('custom'));
    }

    public function testGuardsProviderPluginManagerUsesConfig(): void
    {
        $container = new InMemoryContainer([
            'config' => ['dot_authorization' => ['guards_provider_manager' => ['aliases' => ['a' => 'ArrayGuards']]]],
        ]);

        $this->assertTrue((new GuardsProviderPluginManagerFactory())($container)->has('a'));
    }

    public function testRbacGuardOptionsFactory(): void
    {
        $container = new InMemoryContainer([
            'config' => ['dot_authorization' => ['protection_policy' => GuardInterface::POLICY_DENY]],
        ]);
        $options   = (new RbacGuardOptionsFactory())($container, RbacGuardOptions::class);

        $this->assertInstanceOf(RbacGuardOptions::class, $options);
        $this->assertSame(GuardInterface::POLICY_DENY, $options->getProtectionPolicy());

        $options = (new RbacGuardOptionsFactory())(new InMemoryContainer(['config' => []]), RbacGuardOptions::class);
        $this->assertInstanceOf(RbacGuardOptions::class, $options);
        $this->assertSame(GuardInterface::POLICY_ALLOW, $options->getProtectionPolicy());
    }
}
