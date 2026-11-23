<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Guard\Factory;
use Dot\Rbac\Guard\Guard\GuardPluginManager;
use Dot\Rbac\Guard\Provider\ArrayGuardsProvider;
use DotTest\Rbac\Guard\Fixture\Guard;
use DotTest\Rbac\Guard\Fixture\InMemoryContainer;
use PHPUnit\Framework\TestCase;

class ArrayGuardsProviderTest extends TestCase
{
    private int $builds = 0;

    private function factory(): Factory
    {
        $manager = new GuardPluginManager(new InMemoryContainer());
        $manager->setFactory('low', function (): Guard {
            $this->builds++;
            return new Guard(true, 1);
        });
        $manager->setFactory('high', function (): Guard {
            $this->builds++;
            return new Guard(false, 100);
        });

        return new Factory(new InMemoryContainer(), $manager);
    }

    public function testConstructorRequiresGuardFactory(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Guard factory is required and was not set');

        new ArrayGuardsProvider([]);
    }

    public function testGetGuardsEmptyConfig(): void
    {
        $provider = new ArrayGuardsProvider(['guard_factory' => $this->factory()]);

        $this->assertSame([], $provider->getGuards());
        $this->assertSame([], $provider->getGuardsConfig());
    }

    public function testGetGuardsSortsByPriorityAndCaches(): void
    {
        $config   = [['type' => 'low'], ['type' => 'high']];
        $factory  = $this->factory();
        $provider = new ArrayGuardsProvider(['guard_factory' => $factory, 'guards' => $config]);

        $guards = $provider->getGuards();

        $this->assertSame([100, 1], [$guards[0]->getPriority(), $guards[1]->getPriority()]);
        $this->assertSame($guards, $provider->getGuards());
        $this->assertSame(2, $this->builds);
        $this->assertSame($config, $provider->getGuardsConfig());
        $this->assertSame($factory, $provider->getGuardFactory());
    }

    public function testInvalidGuardConfigThrows(): void
    {
        $provider = new ArrayGuardsProvider(['guard_factory' => $this->factory(), 'guards' => ['low']]);

        $this->expectException(RuntimeException::class);

        $provider->getGuards();
    }
}
