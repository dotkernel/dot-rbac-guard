<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Provider\Factory;
use Dot\Rbac\Guard\Provider\GuardsProviderInterface;
use Dot\Rbac\Guard\Provider\GuardsProviderPluginManager;
use DotTest\Rbac\Guard\Fixture\InMemoryContainer;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    public function testCreateWithoutTypeThrows(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Guard provider type was not specified');

        (new Factory(new InMemoryContainer()))->create(['type' => '']);
    }

    public function testCreatePassesOptions(): void
    {
        $received = null;
        $provider = new class implements GuardsProviderInterface {
            public function getGuards(): array
            {
                return [];
            }
        };
        $manager  = new GuardsProviderPluginManager(new InMemoryContainer());
        $manager->setFactory(
            'custom',
            static function ($container, $name, ?array $options) use (&$received, $provider): GuardsProviderInterface {
                $received = $options;
                return $provider;
            }
        );

        $factory = new Factory(new InMemoryContainer(), $manager);
        $result  = $factory->create(['type' => 'custom', 'options' => ['a' => 1]]);

        $this->assertSame($provider, $result);
        $this->assertSame(['a' => 1], $received);
    }

    public function testGetGuardsProviderPluginManager(): void
    {
        $factory = new Factory(new InMemoryContainer());

        $this->assertSame($factory->getGuardsProviderPluginManager(), $factory->getGuardsProviderPluginManager());
    }
}
