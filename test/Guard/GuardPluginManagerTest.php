<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Guard;

use Dot\Rbac\Guard\Factory\GuardPluginManagerFactory;
use Dot\Rbac\Guard\Guard\GuardInterface;
use Dot\Rbac\Guard\Guard\GuardPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function sprintf;

class GuardPluginManagerTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateInvalidPlugin(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn(['dot_authorization' => ['guard_manager' => []]]);

        $this->expectException(InvalidServiceException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s can only create instances of %s; string is invalid',
                GuardPluginManager::class,
                GuardInterface::class
            )
        );

        $service = (new GuardPluginManagerFactory())($container);
        $service->validate('invalid');
    }
}
