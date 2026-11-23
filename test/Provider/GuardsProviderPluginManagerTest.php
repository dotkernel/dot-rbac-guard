<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Provider;

use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Dot\Rbac\Guard\Exception\InvalidPluginException;
use Dot\Rbac\Guard\Exception\PluginNotFoundException;
use Dot\Rbac\Guard\Guard\GuardPluginManager;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Guard\Provider\ArrayGuardsProvider;
use Dot\Rbac\Guard\Provider\GuardsProviderInterface;
use Dot\Rbac\Guard\Provider\GuardsProviderPluginManager;
use DotTest\Rbac\Guard\Fixture\AuthorizationService;
use DotTest\Rbac\Guard\Fixture\InMemoryContainer;
use PHPUnit\Framework\TestCase;
use stdClass;

class GuardsProviderPluginManagerTest extends TestCase
{
    private InMemoryContainer $container;

    protected function setUp(): void
    {
        $this->container = new InMemoryContainer([
            AuthorizationInterface::class => new AuthorizationService(),
            RbacGuardOptions::class       => new RbacGuardOptions([]),
        ]);
        $this->container->set(GuardPluginManager::class, new GuardPluginManager($this->container));
    }

    public function testBuildsArrayGuardsProvider(): void
    {
        $manager = new GuardsProviderPluginManager($this->container);

        foreach (['ArrayGuards', 'arrayGuardsProvider', ArrayGuardsProvider::class] as $name) {
            $this->assertTrue($manager->has($name));
            $this->assertInstanceOf(ArrayGuardsProvider::class, $manager->build($name, ['guards' => []]));
        }
        $this->assertSame($manager->get('ArrayGuards'), $manager->get(ArrayGuardsProvider::class));
    }

    public function testCustomProviderFromConfig(): void
    {
        $provider = new class implements GuardsProviderInterface {
            public function getGuards(): array
            {
                return [];
            }
        };
        $manager  = new GuardsProviderPluginManager($this->container, [
            'factories' => ['custom' => static fn (): GuardsProviderInterface => $provider],
        ]);

        $this->assertSame($provider, $manager->build('custom'));
        $this->assertSame($provider, $manager->get('custom'));
    }

    public function testUnknownProviderThrows(): void
    {
        $this->expectException(PluginNotFoundException::class);
        $this->expectExceptionMessage('The guards provider "unknown" is not registered.');

        (new GuardsProviderPluginManager($this->container))->get('unknown');
    }

    public function testFactoryThatReturnsWrongTypeThrows(): void
    {
        $manager = new GuardsProviderPluginManager($this->container, [
            'factories' => ['invalid' => static fn (): stdClass => new stdClass()],
        ]);

        $this->expectException(InvalidPluginException::class);

        $manager->get('invalid');
    }

    public function testUnsupportedConfigKeyThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GuardsProviderPluginManager($this->container, ['delegators' => []]);
    }
}
