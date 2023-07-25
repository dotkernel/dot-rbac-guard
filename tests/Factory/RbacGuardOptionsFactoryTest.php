<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Factory\RbacGuardOptionsFactory;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class RbacGuardOptionsFactoryTest extends TestCase
{
    /**
     * @return void
     * @throws Exception
     */
    public function testCanCreateService()
    {
        $container     = $this->createMock(ContainerInterface::class);
        $requestedName = RbacGuardOptions::class;

        $container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn(['dot_authorization' => null]);

        $service = (new RbacGuardOptionsFactory())($container, $requestedName);
        $this->assertInstanceOf(RbacGuardOptions::class, $service);
    }
}
