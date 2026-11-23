<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Guard;

use Dot\Rbac\Guard\Guard\AbstractGuard;
use Dot\Rbac\Guard\Guard\GuardInterface;
use DotTest\Rbac\Guard\Fixture\ExposedGuard;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AbstractGuardTest extends TestCase
{
    /**
     * @return array<string, array{string, string, bool}>
     */
    public static function routeMatchesProvider(): array
    {
        return [
            'exact'                       => ['home', 'home', true],
            'exact, different'            => ['home', 'homes', false],
            'placeholder'                 => ['account-*', 'account-view', true],
            'placeholder needs one char'  => ['account-*', 'account-', false],
            'placeholder with underscore' => ['account-*', 'account-change_password', true],
            'placeholder stops at a dot'  => ['admin.*', 'admin.user.list', false],
            'placeholder stops at slash'  => ['/user/*', '/user/1/edit', false],
            'slash'                       => ['/user/*', '/user/list', true],
            'dot is literal'              => ['user.*', 'userXlist', false],
            'dot'                         => ['user.*', 'user.list', true],
            'regex characters'            => ['a(b)+?*', 'a(b)+?c', true],
            'two placeholders'            => ['*-*', 'user-list', true],
            'mezzio generated name'       => ['/user*', '/user^GET', false],
        ];
    }

    #[DataProvider('routeMatchesProvider')]
    public function testRouteMatches(string $rule, string $routeName, bool $expected): void
    {
        $this->assertSame($expected, (new ExposedGuard())->matches($rule, $routeName));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function methodFromActionProvider(): array
    {
        return [
            'index'      => ['index', 'indexAction'],
            'dash'       => ['edit-user', 'editUserAction'],
            'mixed'      => ['a.b_c', 'aBCAction'],
            'camel case' => ['changePassword', 'changePasswordAction'],
        ];
    }

    #[DataProvider('methodFromActionProvider')]
    public function testGetMethodFromAction(string $action, string $expected): void
    {
        $this->assertSame($expected, ExposedGuard::methodFromAction($action));
    }

    public function testDefaults(): void
    {
        $guard = new ExposedGuard(['protection_policy' => GuardInterface::POLICY_ALLOW]);

        $this->assertSame(GuardInterface::POLICY_ALLOW, $guard->getProtectionPolicy());
        $this->assertSame(AbstractGuard::PRIORITY, $guard->getPriority());
        $this->assertSame([], $guard->getRules());
    }
}
