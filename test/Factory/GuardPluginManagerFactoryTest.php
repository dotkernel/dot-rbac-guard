<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Factory\GuardPluginManagerFactory;
use Dot\Rbac\Guard\Guard\GuardPluginManager;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class GuardPluginManagerFactoryTest extends TestCase
{
    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testCanCreateService(): void
    {
        $config    = [
            'dot_authorization' => [
                'guard_manager' => [],
            ],
        ];
        $container = $this->createMock(ContainerInterface::class);

        $container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $service = (new GuardPluginManagerFactory())($container);
        $this->assertInstanceOf(GuardPluginManager::class, $service);
    }
}
