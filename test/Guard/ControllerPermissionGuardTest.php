<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Guard;

use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Guard\ControllerPermissionGuard;
use Dot\Rbac\Guard\Guard\GuardInterface;
use DotTest\Rbac\Guard\Fixture\AuthorizationService;
use DotTest\Rbac\Guard\Fixture\Requests;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class ControllerPermissionGuardTest extends TestCase
{
    /**
     * @param array<array-key, mixed> $rules
     */
    private function guard(array $rules, string $policy = GuardInterface::POLICY_ALLOW): ControllerPermissionGuard
    {
        return new ControllerPermissionGuard([
            'authorization_service' => new AuthorizationService(['authenticated', 'premium']),
            'protection_policy'     => $policy,
            'rules'                 => $rules,
        ]);
    }

    public function testConstructorRequiresAuthorizationService(): void
    {
        $this->expectException(RuntimeException::class);

        new ControllerPermissionGuard([]);
    }

    public function testSetRules(): void
    {
        $guard = $this->guard([
            ['route' => 'Page', 'actions' => ['premium-content'], 'permissions' => ['premium']],
            ['route' => 'open', 'permissions' => []],
        ]);

        $this->assertSame([
            'page' => ['premiumContentAction' => ['premium']],
            'open' => [0 => []],
        ], $guard->getRules());
        $this->assertSame(ControllerPermissionGuard::PRIORITY, $guard->getPriority());
    }

    public function testRuleWithoutRouteThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->guard([['permissions' => ['premium']]]);
    }

    public function testAuthorizationServiceAccessors(): void
    {
        $guard         = $this->guard([]);
        $authorization = new AuthorizationService();
        $guard->setAuthorizationService($authorization);

        $this->assertSame($authorization, $guard->getAuthorizationService());
    }

    /**
     * @return array<string, array{array<array-key, mixed>, string, ServerRequestInterface, bool}>
     */
    public static function isGrantedProvider(): array
    {
        $rules = [
            ['route' => 'account', 'actions' => ['avatar', 'index'], 'permissions' => ['*']],
            ['route' => 'page', 'actions' => ['premium-content', 'index'], 'permissions' => ['premium']],
            ['route' => 'admin', 'permissions' => ['admin', 'authenticated']],
            [
                'route'       => 'either',
                'permissions' => ['permissions' => ['admin', 'premium'], 'condition' => GuardInterface::CONDITION_OR],
            ],
            ['route' => 'invalidRoute', 'permissions' => []],
        ];

        return [
            'no route result, allow'     => [$rules, 'allow', Requests::withoutRouteResult(), true],
            'no route result, deny'      => [$rules, 'deny', Requests::withoutRouteResult(), false],
            'no rule, allow'             => [$rules, 'allow', Requests::forRoute('testRoute'), true],
            'no rule, deny'              => [$rules, 'deny', Requests::forRoute('testRoute'), false],
            'everyone'                   => [$rules, 'deny', Requests::forRoute('account'), true],
            'premium'                    => [
                $rules,
                'deny',
                Requests::forRoute('page', ['action' => 'premium-content']),
                true,
            ],
            'action without rule, deny'  => [$rules, 'deny', Requests::forRoute('page', ['action' => 'other']), false],
            'and condition, one missing' => [$rules, 'allow', Requests::forRoute('admin'), false],
            'or condition, one granted'  => [$rules, 'deny', Requests::forRoute('either'), true],
            'empty rule, deny'           => [$rules, 'deny', Requests::forRoute('invalidroute'), false],
            'empty rule, allow'          => [$rules, 'allow', Requests::forRoute('invalidroute'), true],
            'mixed case route name'      => [
                [['route' => 'Admin', 'permissions' => ['admin']]],
                'allow',
                Requests::forRoute('Admin'),
                false,
            ],
            'placeholder'                => [
                [['route' => 'account-*', 'permissions' => ['*']]],
                'deny',
                Requests::forRoute('account-view'),
                true,
            ],
            'placeholder, no match'      => [
                [['route' => 'account-*', 'permissions' => ['*']]],
                'deny',
                Requests::forRoute('different-account-route'),
                false,
            ],
            'composite placeholder'      => [
                [['route' => 'user-*-form', 'permissions' => ['*']]],
                'deny',
                Requests::forRoute('user-create-form'),
                true,
            ],
            'slash in rule'              => [
                [['route' => '/user/*', 'permissions' => ['admin']]],
                'allow',
                Requests::forRoute('/user/list'),
                false,
            ],
            'exact rule written first'   => [
                [
                    ['route' => 'admin-edit', 'permissions' => ['premium']],
                    ['route' => 'admin-*', 'permissions' => ['admin']],
                ],
                'deny',
                Requests::forRoute('admin-edit'),
                true,
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
