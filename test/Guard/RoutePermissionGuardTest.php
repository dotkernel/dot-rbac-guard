<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Guard;

use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Guard\GuardInterface;
use Dot\Rbac\Guard\Guard\RoutePermissionGuard;
use DotTest\Rbac\Guard\Fixture\AuthorizationService;
use DotTest\Rbac\Guard\Fixture\Requests;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class RoutePermissionGuardTest extends TestCase
{
    /**
     * @param array<array-key, mixed> $rules
     */
    private function guard(
        array $rules,
        string $policy = GuardInterface::POLICY_ALLOW,
        ?AuthorizationService $authorization = null
    ): RoutePermissionGuard {
        return new RoutePermissionGuard([
            'authorization_service' => $authorization ?? new AuthorizationService(['authenticated', 'premium']),
            'protection_policy'     => $policy,
            'rules'                 => $rules,
        ]);
    }

    public function testConstructorRequiresAuthorizationService(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Authorization service is required by this guard and was not set');

        new RoutePermissionGuard(null);
    }

    public function testSetRules(): void
    {
        $guard = $this->guard(['Premium' => 'premium', 'Open']);

        $this->assertSame(['premium' => ['premium'], 'open' => []], $guard->getRules());
        $this->assertSame(RoutePermissionGuard::PRIORITY, $guard->getPriority());
    }

    public function testSetRulesRejectsNonStringRouteName(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->guard([['premium']]);
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
        $or = ['permissions' => ['admin', 'premium'], 'condition' => GuardInterface::CONDITION_OR];

        return [
            'no route result, allow'     => [[], 'allow', Requests::withoutRouteResult(), true],
            'no route result, deny'      => [[], 'deny', Requests::withoutRouteResult(), false],
            'no rule, allow'             => [['premium' => ['premium']], 'allow', Requests::forRoute('other'), true],
            'no rule, deny'              => [['premium' => ['premium']], 'deny', Requests::forRoute('other'), false],
            'everyone'                   => [['home' => ['*']], 'deny', Requests::forRoute('home'), true],
            'all permissions granted'    => [
                ['page' => ['authenticated', 'premium']],
                'deny',
                Requests::forRoute('page'),
                true,
            ],
            'one permission missing'     => [
                ['page' => ['authenticated', 'admin']],
                'allow',
                Requests::forRoute('page'),
                false,
            ],
            'or condition, one granted'  => [['page' => $or], 'deny', Requests::forRoute('page'), true],
            'or condition, none granted' => [
                ['page' => ['permissions' => ['admin'], 'condition' => 'OR']],
                'allow',
                Requests::forRoute('page'),
                false,
            ],
            'nested and condition'       => [
                ['page' => ['permissions' => ['premium']]],
                'deny',
                Requests::forRoute('page'),
                true,
            ],
            'empty rule, deny'           => [['secret'], 'deny', Requests::forRoute('secret'), false],
            'empty rule, allow'          => [['secret'], 'allow', Requests::forRoute('secret'), true],
            'empty nested rule, deny'    => [
                ['secret' => ['permissions' => [], 'condition' => 'OR']],
                'deny',
                Requests::forRoute('secret'),
                false,
            ],
            'mixed case route name'      => [['Admin' => ['admin']], 'allow', Requests::forRoute('ADMIN'), false],
            'placeholder'                => [
                ['account-*' => ['premium']],
                'deny',
                Requests::forRoute('account-view'),
                true,
            ],
            'placeholder, no match'      => [
                ['account-*' => ['premium']],
                'deny',
                Requests::forRoute('different-account-route'),
                false,
            ],
            'slash in rule'              => [
                ['/user/*' => ['admin']],
                'allow',
                Requests::forRoute('/user/list'),
                false,
            ],
            'first rule wins'            => [
                ['admin-edit' => ['premium'], 'admin-*' => ['admin']],
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

    public function testAndConditionStopsAtFirstMissingPermission(): void
    {
        $authorization = new AuthorizationService(['premium']);
        $guard         = $this->guard(['page' => ['admin', 'premium']], 'allow', $authorization);

        $this->assertFalse($guard->isGranted(Requests::forRoute('page')));
        $this->assertSame(['admin'], $authorization->checked);
    }

    public function testInvalidConditionThrows(): void
    {
        $guard = $this->guard(['page' => ['permissions' => ['premium'], 'condition' => 'XOR']]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Condition must be either "AND" or "OR", string given');

        $guard->isGranted(Requests::forRoute('page'));
    }
}
