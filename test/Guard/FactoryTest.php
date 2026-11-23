<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Guard;

use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Guard\Factory;
use Dot\Rbac\Guard\Guard\GuardPluginManager;
use DotTest\Rbac\Guard\Fixture\Guard;
use DotTest\Rbac\Guard\Fixture\InMemoryContainer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    /**
     * @return array<string, array{array<array-key, mixed>}>
     */
    public static function invalidSpecsProvider(): array
    {
        return [
            'no type'         => [[]],
            'empty type'      => [['type' => '']],
            'non-string type' => [['type' => 1]],
        ];
    }

    /**
     * @param array<array-key, mixed> $specs
     */
    #[DataProvider('invalidSpecsProvider')]
    public function testCreateWithoutTypeThrows(array $specs): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Guard type was not provided');

        (new Factory(new InMemoryContainer()))->create($specs);
    }

    /** @var list<array<array-key, mixed>|null> */
    private array $received = [];

    public function testCreatePassesOptions(): void
    {
        $manager = new GuardPluginManager(new InMemoryContainer());
        $manager->setFactory('custom', function ($container, $name, ?array $options): Guard {
            $this->received[] = $options;
            return new Guard(true);
        });

        $factory = new Factory(new InMemoryContainer(), $manager);
        $factory->create(['type' => 'custom', 'options' => ['rules' => []]]);
        $factory->create(['type' => 'custom', 'options' => 'invalid']);

        $this->assertSame([['rules' => []], null], $this->received);
    }

    public function testGetGuardPluginManager(): void
    {
        $manager = new GuardPluginManager(new InMemoryContainer());

        $this->assertSame($manager, (new Factory(new InMemoryContainer(), $manager))->getGuardPluginManager());

        $factory = new Factory(new InMemoryContainer());
        $this->assertSame($factory->getGuardPluginManager(), $factory->getGuardPluginManager());
    }
}
