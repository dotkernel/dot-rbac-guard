<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Guard;

use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Dot\Rbac\Guard\Exception\InvalidPluginException;
use Dot\Rbac\Guard\Exception\PluginNotFoundException;
use Dot\Rbac\Guard\Guard\ControllerGuard;
use Dot\Rbac\Guard\Guard\ControllerPermissionGuard;
use Dot\Rbac\Guard\Guard\GuardPluginManager;
use Dot\Rbac\Guard\Guard\RouteGuard;
use Dot\Rbac\Guard\Guard\RoutePermissionGuard;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Role\RoleServiceInterface;
use DotTest\Rbac\Guard\Fixture\AuthorizationService;
use DotTest\Rbac\Guard\Fixture\Guard;
use DotTest\Rbac\Guard\Fixture\InMemoryContainer;
use DotTest\Rbac\Guard\Fixture\RoleService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;

class GuardPluginManagerTest extends TestCase
{
    private InMemoryContainer $container;

    protected function setUp(): void
    {
        $this->container = new InMemoryContainer([
            RoleServiceInterface::class   => new RoleService(),
            AuthorizationInterface::class => new AuthorizationService(),
            RbacGuardOptions::class       => new RbacGuardOptions([]),
        ]);
    }

    /**
     * @return array<string, array{string, class-string}>
     */
    public static function defaultGuardsProvider(): array
    {
        return [
            'Route'                => ['Route', RouteGuard::class],
            'routeGuard'           => ['routeGuard', RouteGuard::class],
            'RoutePermission'      => ['RoutePermission', RoutePermissionGuard::class],
            'Controller'           => ['Controller', ControllerGuard::class],
            'ControllerPermission' => ['ControllerPermission', ControllerPermissionGuard::class],
            'class name'           => [RouteGuard::class, RouteGuard::class],
        ];
    }

    /**
     * @param class-string $expected
     */
    #[DataProvider('defaultGuardsProvider')]
    public function testBuildsDefaultGuards(string $name, string $expected): void
    {
        $manager = new GuardPluginManager($this->container);

        $this->assertTrue($manager->has($name));
        $this->assertInstanceOf($expected, $manager->build($name, ['rules' => []]));
    }

    public function testBuildPassesOptions(): void
    {
        $manager = new GuardPluginManager($this->container);
        $guard   = $manager->build('Route', ['rules' => ['home' => ['*']], 'protection_policy' => 'allow']);

        $this->assertInstanceOf(RouteGuard::class, $guard);
        $this->assertSame(['home' => ['*']], $guard->getRules());
        $this->assertSame('allow', $guard->getProtectionPolicy());
    }

    public function testGetReturnsSharedInstanceAndBuildReturnsNewInstance(): void
    {
        $manager = new GuardPluginManager($this->container);

        $this->assertSame($manager->get('Route'), $manager->get(RouteGuard::class));
        $this->assertNotSame($manager->build('Route'), $manager->build('Route'));
    }

    public function testCustomFactoryAndAliasFromConfig(): void
    {
        $guard   = new Guard(true);
        $manager = new GuardPluginManager($this->container, [
            'factories' => ['custom' => static fn (): Guard => $guard],
            'aliases'   => ['Custom' => 'custom'],
        ]);

        $this->assertSame($guard, $manager->build('Custom'));
    }

    public function testConfigOverridesDefaultFactory(): void
    {
        $guard   = new Guard(true);
        $manager = new GuardPluginManager($this->container, [
            'factories' => [RouteGuard::class => static fn (): Guard => $guard],
        ]);

        $this->assertSame($guard, $manager->build('Route'));
    }

    public function testFactoryReceivesContainerNameAndOptions(): void
    {
        $received = [];
        $manager  = new GuardPluginManager($this->container);
        $manager->setFactory(
            'custom',
            static function (ContainerInterface $container, string $name, ?array $options) use (&$received): Guard {
                $received = [$container, $name, $options];
                return new Guard(true);
            }
        );
        $manager->setAlias('alias', 'custom');
        $manager->build('alias', ['a' => 1]);

        $this->assertSame([$this->container, 'custom', ['a' => 1]], $received);
    }

    public function testUnknownGuardThrowsNotFound(): void
    {
        $manager = new GuardPluginManager($this->container);

        $this->assertFalse($manager->has('unknown'));
        $this->expectException(PluginNotFoundException::class);
        $this->expectExceptionMessage('The guard "unknown" is not registered.');

        $manager->get('unknown');
    }

    public function testNotFoundIsAPsrException(): void
    {
        $manager = new GuardPluginManager($this->container);

        try {
            $manager->build('unknown');
            $this->fail('No exception was thrown');
        } catch (NotFoundExceptionInterface $exception) {
            $this->assertSame('The guard "unknown" is not registered.', $exception->getMessage());
        }
    }

    public function testFactoryThatReturnsWrongTypeThrows(): void
    {
        $manager = new GuardPluginManager($this->container, [
            'factories' => ['invalid' => static fn (): stdClass => new stdClass()],
        ]);

        $this->expectException(InvalidPluginException::class);
        $this->expectExceptionMessage('The guard "invalid" must implement');

        $manager->build('invalid');
    }

    public function testInvalidFactoryThrows(): void
    {
        $this->expectException(InvalidPluginException::class);

        new GuardPluginManager($this->container, ['factories' => ['invalid' => 42]]);
    }

    public function testFactoryClassThatDoesNotExistThrows(): void
    {
        $manager = new GuardPluginManager($this->container, ['factories' => ['invalid' => 'No\\Such\\Factory']]);

        $this->expectException(InvalidPluginException::class);

        $manager->build('invalid');
    }

    public function testFactoryClassThatIsNotCallableThrows(): void
    {
        $manager = new GuardPluginManager($this->container, ['factories' => ['invalid' => stdClass::class]]);

        $this->expectException(InvalidPluginException::class);

        $manager->build('invalid');
    }

    public function testUnsupportedConfigKeyThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Remove the keys "invokables"');

        new GuardPluginManager($this->container, ['invokables' => []]);
    }

    public function testConfigKeyThatIsNotAnArrayThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GuardPluginManager($this->container, ['factories' => 'invalid']);
    }

    public function testAliasThatIsNotAStringThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GuardPluginManager($this->container, ['aliases' => ['a' => 1]]);
    }

    public function testAliasCycleThrows(): void
    {
        $manager = new GuardPluginManager($this->container, ['aliases' => ['a' => 'b', 'b' => 'a']]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('is part of a cycle');

        $manager->has('a');
    }
}
