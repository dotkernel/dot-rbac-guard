<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Provider\Factory;
use Dot\Rbac\Guard\Provider\GuardsProviderInterface;
use Dot\Rbac\Guard\Provider\GuardsProviderPluginManager;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class FactoryTest extends TestCase
{
    protected Factory $subject;
    protected ContainerInterface $container;

    public function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);

        $this->subject = new Factory($this->container);
    }

    public function testCreateRuntimeException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Guard provider type was not specified');
        $this->subject->create([]);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testCreate()
    {
        $type                        = 'arrayGuardsProvider';
        $guardsProviderPluginManager = $this->createMock(GuardsProviderPluginManager::class);
        $guardsProviderPluginManager->expects($this->once())
            ->method('get')
            ->with($type, null)
            ->willReturn(new class implements GuardsProviderInterface {
                public function getGuards(): array
                {
                    return [];
                }
            });
        $subject = new Factory($this->container, $guardsProviderPluginManager);

        $result = $subject->create(
            [
                'type' => $type,
            ]
        );

        $this->assertInstanceOf(GuardsProviderInterface::class, $result);
    }

    public function testGetGuardsProviderPluginManager()
    {
        $guardsProviderPluginManager = new GuardsProviderPluginManager();
        $subject                     = new Factory($this->container, $guardsProviderPluginManager);

        $result = $subject->getGuardsProviderPluginManager();
        $this->assertInstanceOf(GuardsProviderPluginManager::class, $result);
    }
}
