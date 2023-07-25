<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Factory\GuardsProviderPluginManagerFactory;
use Dot\Rbac\Guard\Provider\GuardsProviderPluginManager;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class GuardsProviderPluginManagerFactoryTest extends TestCase
{
    /**
     * @return void
     * @throws Exception
     */
    public function testCanCreateService()
    {
        $container = $this->createMock(ContainerInterface::class);
        $config    = [
            'dot_authorization' => [
                'guards_provider_manager' => [],
            ],
        ];

        $container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $service = (new GuardsProviderPluginManagerFactory())($container);
        $this->assertInstanceOf(GuardsProviderPluginManager::class, $service);
    }
}
