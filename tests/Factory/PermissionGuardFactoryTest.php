<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Factory\PermissionGuardFactory;
use Dot\Rbac\Guard\Guard\GuardInterface;
use Dot\Rbac\Guard\Guard\RoutePermissionGuard;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class PermissionGuardFactoryTest extends TestCase
{
    /**
     * @return void
     * @throws Exception
     */
    public function testWillNotCreateWithoutRoleService()
    {
        $options       = [
            'authorization_service' => 'noAuthorizationService',
            'protection_policy'     => GuardInterface::POLICY_ALLOW,
            'rules'                 => [],
        ];
        $requestedName = RoutePermissionGuard::class;
        $container     = $this->createMock(ContainerInterface::class);

        $container->expects($this->once())
            ->method('has')
            ->with($options['authorization_service'])
            ->willReturn(true);
        $container->method('get')->willReturnMap([
            [$options['authorization_service'], []],
            [RbacGuardOptions::class, new RbacGuardOptions(null)],
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Authorization service is required by this guard and was not set');
        (new PermissionGuardFactory())($container, $requestedName, $options);
    }
}
