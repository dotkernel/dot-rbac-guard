<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Factory\GuardsProviderFactory;
use Dot\Rbac\Guard\Guard\GuardPluginManager;
use Dot\Rbac\Guard\Provider\ArrayGuardsProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class GuardsProviderFactoryTest extends TestCase
{
    /**
     * @return void
     * @throws Exception
     */
    public function testWillNotCreateWithoutGuardFactory()
    {
        $container     = $this->createMock(ContainerInterface::class);
        $options       = [
            'guard_factory' => 'stringTest',
        ];
        $requestedName = ArrayGuardsProvider::class;

        $container->expects($this->once())
            ->method('get')
            ->with($options['guard_factory'])
            ->willReturn(false);
        $container->expects($this->once())
            ->method('has')
            ->with($options['guard_factory'])
            ->willReturn(true);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Guard factory is required and was not set');
        (new GuardsProviderFactory())($container, $requestedName, $options);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testCanCreateService()
    {
        $container     = $this->createMock(ContainerInterface::class);
        $requestedName = ArrayGuardsProvider::class;

        $container->expects($this->once())
            ->method('get')
            ->with(GuardPluginManager::class)
            ->willReturn(new GuardPluginManager());

        $service = (new GuardsProviderFactory())($container, $requestedName);
        $this->assertInstanceOf(ArrayGuardsProvider::class, $service);
    }
}
