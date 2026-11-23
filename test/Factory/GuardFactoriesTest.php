<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Factory;

use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Factory\GuardFactory;
use Dot\Rbac\Guard\Factory\GuardsProviderFactory;
use Dot\Rbac\Guard\Factory\PermissionGuardFactory;
use Dot\Rbac\Guard\Guard\Factory;
use Dot\Rbac\Guard\Guard\GuardInterface;
use Dot\Rbac\Guard\Guard\GuardPluginManager;
use Dot\Rbac\Guard\Guard\RouteGuard;
use Dot\Rbac\Guard\Guard\RoutePermissionGuard;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Guard\Provider\ArrayGuardsProvider;
use Dot\Rbac\Role\RoleServiceInterface;
use DotTest\Rbac\Guard\Fixture\AuthorizationService;
use DotTest\Rbac\Guard\Fixture\InMemoryContainer;
use DotTest\Rbac\Guard\Fixture\RoleService;
use DotTest\Rbac\Guard\Fixture\ServiceNotFoundException;
use PHPUnit\Framework\TestCase;

class GuardFactoriesTest extends TestCase
{
    private InMemoryContainer $container;

    protected function setUp(): void
    {
        $this->container = new InMemoryContainer([
            RoleServiceInterface::class   => new RoleService(['default']),
            AuthorizationInterface::class => new AuthorizationService(),
            RbacGuardOptions::class       => new RbacGuardOptions(['protection_policy' => GuardInterface::POLICY_DENY]),
            'custom.roles'                => new RoleService(['custom']),
            'custom.authorization'        => new AuthorizationService(),
        ]);
    }

    public function testGuardFactoryUsesDefaults(): void
    {
        $guard = (new GuardFactory())($this->container, RouteGuard::class);

        $this->assertInstanceOf(RouteGuard::class, $guard);
        $this->assertSame($this->container->get(RoleServiceInterface::class), $guard->getRoleService());
        $this->assertSame(GuardInterface::POLICY_DENY, $guard->getProtectionPolicy());
        $this->assertSame([], $guard->getRules());
    }

    public function testGuardFactoryUsesOptions(): void
    {
        $guard = (new GuardFactory())($this->container, RouteGuard::class, [
            'role_service'      => 'custom.roles',
            'protection_policy' => GuardInterface::POLICY_ALLOW,
            'rules'             => ['home' => ['*']],
        ]);

        $this->assertInstanceOf(RouteGuard::class, $guard);
        $this->assertSame($this->container->get('custom.roles'), $guard->getRoleService());
        $this->assertSame(GuardInterface::POLICY_ALLOW, $guard->getProtectionPolicy());
        $this->assertSame(['home' => ['*']], $guard->getRules());
    }

    public function testGuardFactoryWithoutRoleServiceThrows(): void
    {
        $this->expectException(ServiceNotFoundException::class);

        $container = new InMemoryContainer([RbacGuardOptions::class => new RbacGuardOptions([])]);
        (new GuardFactory())($container, RouteGuard::class);
    }

    public function testPermissionGuardFactoryUsesDefaults(): void
    {
        $guard = (new PermissionGuardFactory())($this->container, RoutePermissionGuard::class, [
            'protection_policy' => 'invalid',
            'rules'             => 'invalid',
        ]);

        $this->assertInstanceOf(RoutePermissionGuard::class, $guard);
        $this->assertSame($this->container->get(AuthorizationInterface::class), $guard->getAuthorizationService());
        $this->assertSame(GuardInterface::POLICY_DENY, $guard->getProtectionPolicy());
        $this->assertSame([], $guard->getRules());
    }

    public function testPermissionGuardFactoryUsesOptions(): void
    {
        $guard = (new PermissionGuardFactory())($this->container, RoutePermissionGuard::class, [
            'authorization_service' => 'custom.authorization',
        ]);

        $this->assertInstanceOf(RoutePermissionGuard::class, $guard);
        $this->assertSame($this->container->get('custom.authorization'), $guard->getAuthorizationService());
    }

    public function testGuardsProviderFactoryCreatesGuardFactory(): void
    {
        $this->container->set(GuardPluginManager::class, new GuardPluginManager($this->container));

        $provider = (new GuardsProviderFactory())($this->container, ArrayGuardsProvider::class, [
            'guards' => [['type' => 'Route', 'options' => ['rules' => []]]],
        ]);

        $this->assertInstanceOf(ArrayGuardsProvider::class, $provider);
        $this->assertCount(1, $provider->getGuards());
    }

    public function testGuardsProviderFactoryUsesGuardFactoryService(): void
    {
        $factory = new Factory($this->container, new GuardPluginManager($this->container));
        $this->container->set('custom.factory', $factory);

        $provider = (new GuardsProviderFactory())($this->container, ArrayGuardsProvider::class, [
            'guard_factory' => 'custom.factory',
        ]);

        $this->assertInstanceOf(ArrayGuardsProvider::class, $provider);
        $this->assertSame($factory, $provider->getGuardFactory());
    }

    public function testGuardsProviderFactoryWithoutGuardFactoryThrows(): void
    {
        $this->container->set('not.a.factory', 'string');

        $this->expectException(RuntimeException::class);

        (new GuardsProviderFactory())(
            $this->container,
            ArrayGuardsProvider::class,
            ['guard_factory' => 'not.a.factory']
        );
    }
}
