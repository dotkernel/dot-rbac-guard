<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Factory\GuardsProviderPluginManagerFactory;
use Dot\Rbac\Guard\Provider\GuardsProviderInterface;
use Dot\Rbac\Guard\Provider\GuardsProviderPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function sprintf;

class GuardsProviderPluginManagerTest extends TestCase
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
            ->willReturn(['dot_authorization' => ['guards_provider_manager' => []]]);

        $this->expectException(InvalidServiceException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s can only create instances of %s; string is invalid',
                GuardsProviderPluginManager::class,
                GuardsProviderInterface::class
            )
        );

        $service = (new GuardsProviderPluginManagerFactory())($container);
        $service->validate('invalid');
    }
}
