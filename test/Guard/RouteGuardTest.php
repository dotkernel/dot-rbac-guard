<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Guard;

use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Guard\GuardInterface;
use Dot\Rbac\Guard\Guard\RouteGuard;
use DotTest\Rbac\Guard\Fixture\Requests;
use DotTest\Rbac\Guard\Fixture\RoleService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class RouteGuardTest extends TestCase
{
    /**
     * @param array<array-key, mixed> $rules
     */
    private function guard(array $rules, string $policy = GuardInterface::POLICY_ALLOW): RouteGuard
    {
        return new RouteGuard([
            'role_service'      => new RoleService(['user']),
            'protection_policy' => $policy,
            'rules'             => $rules,
        ]);
    }

    public function testConstructorRequiresRoleService(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('RoleService is required by this guard and was not set');

        new RouteGuard([]);
    }

    public function testDefaultPolicyIsDeny(): void
    {
        $guard = new RouteGuard(['role_service' => new RoleService()]);

        $this->assertSame(GuardInterface::POLICY_DENY, $guard->getProtectionPolicy());
        $this->assertSame(RouteGuard::PRIORITY, $guard->getPriority());
    }

    public function testInvalidPolicyIsIgnored(): void
    {
        $guard = new RouteGuard(['role_service' => new RoleService(), 'protection_policy' => 'somePolicy']);

        $this->assertSame(GuardInterface::POLICY_DENY, $guard->getProtectionPolicy());
    }

    public function testSetRulesLowercasesRouteNames(): void
    {
        $guard = $this->guard(['Account' => 'user', 'Login']);

        $this->assertSame(['account' => ['user'], 'login' => []], $guard->getRules());
    }

    public function testSetRulesRejectsNonStringRouteName(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->guard([['user']]);
    }

    public function testRoleServiceAccessors(): void
    {
        $guard       = $this->guard([]);
        $roleService = new RoleService(['admin']);
        $guard->setRoleService($roleService);

        $this->assertSame($roleService, $guard->getRoleService());
    }

    /**
     * @return array<string, array{array<array-key, mixed>, string, ServerRequestInterface, bool}>
     */
    public static function isGrantedProvider(): array
    {
        return [
            'no route result, allow'          => [[], 'allow', Requests::withoutRouteResult(), true],
            'no route result, deny'           => [[], 'deny', Requests::withoutRouteResult(), false],
            'route failure, deny'             => [[], 'deny', Requests::forRouteFailure(), false],
            'no rule, allow'                  => [['home' => ['*']], 'allow', Requests::forRoute('other'), true],
            'no rule, deny'                   => [['home' => ['*']], 'deny', Requests::forRoute('other'), false],
            'everyone'                        => [['home' => ['*']], 'deny', Requests::forRoute('home'), true],
            'role matches'                    => [['account' => ['user']], 'deny', Requests::forRoute('account'), true],
            'role does not match'             => [
                ['premium' => ['admin']],
                'allow',
                Requests::forRoute('premium'),
                false,
            ],
            'mixed case route name'           => [
                ['Premium' => ['admin']],
                'allow',
                Requests::forRoute('PREMIUM'),
                false,
            ],
            'placeholder matches'             => [
                ['account-*' => ['user']],
                'deny',
                Requests::forRoute('account-view'),
                true,
            ],
            'placeholder does not match'      => [
                ['account-*' => ['user']],
                'deny',
                Requests::forRoute('other-account'),
                false,
            ],
            'composite placeholder'           => [
                ['user-*-form' => ['user']],
                'deny',
                Requests::forRoute('user-create-form'),
                true,
            ],
            'composite placeholder, no match' => [
                ['user-*-form' => ['user']],
                'deny',
                Requests::forRoute('user-create-avatar'),
                false,
            ],
            'placeholder stops at a dot'      => [
                ['admin.*' => ['admin']],
                'allow',
                Requests::forRoute('admin.user.list'),
                true,
            ],
            'slash in rule'                   => [
                ['/user/*' => ['admin']],
                'allow',
                Requests::forRoute('/user/list'),
                false,
            ],
            'dot is not a wildcard'           => [
                ['user.*' => ['admin']],
                'allow',
                Requests::forRoute('userXlist'),
                true,
            ],
            'regex characters in rule'        => [
                ['a(b)+*' => ['admin']],
                'allow',
                Requests::forRoute('a(b)+c'),
                false,
            ],
            'double colon in rule'            => [
                ['admin::*' => ['admin']],
                'allow',
                Requests::forRoute('admin::list-admin'),
                false,
            ],
            'first rule wins, exact first'    => [
                ['admin-edit' => ['user'], 'admin-*' => ['admin']],
                'deny',
                Requests::forRoute('admin-edit'),
                true,
            ],
            'first rule wins, wildcard first' => [
                ['admin-*' => ['admin'], 'admin-edit' => ['user']],
                'allow',
                Requests::forRoute('admin-edit'),
                false,
            ],
            'numeric route name'              => [['404'], 'allow', Requests::forRoute('404'), false],
        ];
    }

    /**
     * @param array<array-key, mixed> $rules
     */
    #[DataProvider('isGrantedProvider')]
    public function testIsGranted(array $rules, string $policy, ServerRequestInterface $request, bool $expected): void
    {
        $this->assertSame($expected, $this->guard($rules, $policy)->isGranted($request));
    }
}
