<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Guard;

use Dot\Rbac\Guard\Guard\Factory;
use Dot\Rbac\Guard\Guard\GuardInterface;
use Dot\Rbac\Guard\Guard\GuardPluginManager;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class FactoryTest extends TestCase
{
    /**
     * @return void
     * @throws Exception
     */
    public function testCreateRuntimeException()
    {
        $container = $this->createMock(ContainerInterface::class);
        $subject   = new Factory($container);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Guard type was not provided');
        $subject->create([]);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testCreate()
    {
        $container           = $this->createMock(ContainerInterface::class);
        $guardsPluginManager = $this->createMock(GuardPluginManager::class);

        $guardsPluginManager->expects($this->once())
            ->method('get')
            ->with('testType', null)
            ->willReturn(new class implements GuardInterface {
                public function isGranted(ServerRequestInterface $request): bool
                {
                    return true;
                }

                public function getPriority(): int
                {
                    return 1;
                }
            });
        $subject = new Factory($container, $guardsPluginManager);

        $result = $subject->create(['type' => 'testType']);
        $this->assertInstanceOf(GuardInterface::class, $result);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testGetGuardPluginManager()
    {
        $container           = $this->createMock(ContainerInterface::class);
        $guardsPluginManager = $this->createMock(GuardPluginManager::class);

        $subject = new Factory($container, $guardsPluginManager);

        $result = $subject->getGuardPluginManager();
        $this->assertInstanceOf(GuardPluginManager::class, $result);
    }
}
