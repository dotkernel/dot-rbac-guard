<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Factory\GuardFactory;
use Dot\Rbac\Guard\Guard\GuardInterface;
use Dot\Rbac\Guard\Guard\RouteGuard;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class GuardFactoryTest extends TestCase
{
    /**
     * @return void
     * @throws Exception
     */
    public function testWillNotCreateWithoutRoleService()
    {
        $options       = [
            'role_service'      => 'noRoleService',
            'protection_policy' => GuardInterface::POLICY_ALLOW,
            'rules'             => [],
        ];
        $requestedName = RouteGuard::class;
        $container     = $this->createMock(ContainerInterface::class);

        $container->expects($this->once())
            ->method('has')
            ->with($options['role_service'])
            ->willReturn(true);
        $container->method('get')->willReturnMap([
            [$options['role_service'], []],
            [RbacGuardOptions::class, new RbacGuardOptions(null)],
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('RoleService is required by this guard and was not set');
        (new GuardFactory())($container, $requestedName, $options);
    }
}
