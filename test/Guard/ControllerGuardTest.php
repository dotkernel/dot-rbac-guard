<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Guard;

use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Guard\ControllerGuard;
use Dot\Rbac\Guard\Guard\GuardInterface;
use DotTest\Rbac\Guard\Fixture\Requests;
use DotTest\Rbac\Guard\Fixture\RoleService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class ControllerGuardTest extends TestCase
{
    /**
     * @param array<array-key, mixed> $rules
     */
    private function guard(array $rules, string $policy = GuardInterface::POLICY_ALLOW): ControllerGuard
    {
        return new ControllerGuard([
            'role_service'      => new RoleService(['user']),
            'protection_policy' => $policy,
            'rules'             => $rules,
        ]);
    }

    public function testConstructorRequiresRoleService(): void
    {
        $this->expectException(RuntimeException::class);

        new ControllerGuard(null);
    }

    public function testSetRules(): void
    {
        $guard = $this->guard([
            ['route' => 'Account', 'actions' => ['change-password', 'index'], 'roles' => ['user']],
            ['route' => 'page', 'roles' => 'admin'],
        ]);

        $this->assertSame([
            'account' => ['changePasswordAction' => ['user'], 'indexAction' => ['user']],
            'page'    => [0 => ['admin']],
        ], $guard->getRules());
        $this->assertSame(ControllerGuard::PRIORITY, $guard->getPriority());
    }

    public function testRuleWithoutRouteThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Each controller guard rule must have a "route" key with a string value');

        $this->guard([['roles' => ['admin']]]);
    }

    public function testRuleWithNonStringActionThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->guard([['route' => 'page', 'actions' => [1], 'roles' => ['admin']]]);
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
        $account = [
            'route'   => 'account',
            'actions' => ['avatar', 'details', 'change-password', 'index'],
            'roles'   => ['user'],
        ];
        $admin   = ['route' => 'admin', 'actions' => [], 'roles' => ['admin']];
        $open    = ['route' => 'home', 'roles' => ['*']];
        $rules   = [$account, $admin, $open];

        return [
            'no route result, allow'      => [$rules, 'allow', Requests::withoutRouteResult(), true],
            'no route result, deny'       => [$rules, 'deny', Requests::withoutRouteResult(), false],
            'no rule, allow'              => [$rules, 'allow', Requests::forRoute('other'), true],
            'no rule, deny'               => [$rules, 'deny', Requests::forRoute('other'), false],
            'default action is index'     => [$rules, 'deny', Requests::forRoute('account'), true],
            'action matches role'         => [
                $rules,
                'deny',
                Requests::forRoute('account', ['action' => 'change-password']),
                true,
            ],
            'action without rule, allow'  => [
                $rules,
                'allow',
                Requests::forRoute('account', ['action' => 'delete']),
                true,
            ],
            'action without rule, deny'   => [
                $rules,
                'deny',
                Requests::forRoute('account', ['action' => 'delete']),
                false,
            ],
            'all actions, role missing'   => [
                $rules,
                'allow',
                Requests::forRoute('admin', ['action' => 'edit']),
                false,
            ],
            'non-string action is index'  => [$rules, 'deny', Requests::forRoute('account', ['action' => ['x']]), true],
            'everyone'                    => [$rules, 'deny', Requests::forRoute('home'), true],
            'empty roles'                 => [
                [['route' => 'page', 'roles' => []]],
                'allow',
                Requests::forRoute('page'),
                false,
            ],
            'mixed case route name'       => [
                [['route' => 'Admin', 'roles' => ['admin']]],
                'allow',
                Requests::forRoute('ADMIN'),
                false,
            ],
            'placeholder'                 => [
                [['route' => 'account-*', 'roles' => ['user']]],
                'deny',
                Requests::forRoute('account-view'),
                true,
            ],
            'composite placeholder'       => [
                [['route' => 'user-*-form', 'roles' => ['user']]],
                'deny',
                Requests::forRoute('user-create-form'),
                true,
            ],
            'placeholder, no match'       => [
                [['route' => 'user-*-form', 'roles' => ['user']]],
                'deny',
                Requests::forRoute('user-create-avatar'),
                false,
            ],
            'slash in rule'               => [
                [['route' => '/user/*', 'roles' => ['admin']]],
                'allow',
                Requests::forRoute('/user/list'),
                false,
            ],
            'exact rule written first'    => [
                [['route' => 'admin-edit', 'roles' => ['user']], ['route' => 'admin-*', 'roles' => ['admin']]],
                'deny',
                Requests::forRoute('admin-edit'),
                true,
            ],
            'wildcard rule written first' => [
                [['route' => 'admin-*', 'roles' => ['admin']], ['route' => 'admin-edit', 'roles' => ['user']]],
                'allow',
                Requests::forRoute('admin-edit'),
                false,
            ],
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
