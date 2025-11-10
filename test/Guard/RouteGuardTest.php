<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Guard;

use Dot\Rbac\Guard\Guard\RouteGuard;
use Dot\Rbac\Role\RoleServiceInterface;
use Laminas\Diactoros\ServerRequest;
use Mezzio\Router\RouteResult;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class RouteGuardTest extends TestCase
{
    protected RouteGuard $subject;

    protected RoleServiceInterface $mockRoleServiceClass;

    protected array $rules = [
        'actions' => [
            'avatar',
            'details',
            'changePassword',
            'deleteAccount',
            'index',
        ],
        'test'    => ['*'],
    ];

    public function setUp(): void
    {
        $this->mockRoleServiceClass = new class implements RoleServiceInterface {
            public function getIdentity(): ?string
            {
                return null;
            }

            public function getGuestRole(): string
            {
                return 'role';
            }

            public function getIdentityRoles(): array
            {
                return [];
            }

            public function matchIdentityRoles(array $roles): bool
            {
                return true;
            }
        };

        $this->subject = new RouteGuard(
            [
                'protection_policy' => 'somePolicy',
                'role_service'      => $this->mockRoleServiceClass,
            ]
        );
    }

    public function testSetRules(): void
    {
        $this->subject->setRules($this->rules);
        $this->assertIsArray($this->subject->getRules());
    }

    public function testSetRoleService(): void
    {
        $this->subject->setRoleService($this->mockRoleServiceClass);
        $result = $this->subject->getRoleService();
        $this->assertInstanceOf(RoleServiceInterface::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testIsNotGrantedRulesNotSet(): void
    {
        $request = $this->createMock(ServerRequest::class);

        $result = $this->subject->isGranted($request);
        $this->assertFalse($result);
    }

    /**
     * @throws Exception
     */
    public function testIsNotGrantedNullAllowedRoles(): void
    {
        $request     = $this->createMock(ServerRequest::class);
        $routeResult = $this->createMock(RouteResult::class);

        $request->expects($this->once())
            ->method('getAttribute')
            ->with(RouteResult::class)
            ->willReturn($routeResult);
        $routeResult->expects($this->atLeastOnce())
            ->method('getMatchedRouteName')
            ->willReturn('testRoute');

        $result = $this->subject->isGranted($request);
        $this->assertFalse($result);
    }

    /**
     * @throws Exception
     */
    public function testIsGrantedEverything(): void
    {
        $request     = $this->createMock(ServerRequest::class);
        $routeResult = $this->createMock(RouteResult::class);

        $request->expects($this->once())
            ->method('getAttribute')
            ->with(RouteResult::class)
            ->willReturn($routeResult);
        $routeResult->expects($this->atLeastOnce())
            ->method('getMatchedRouteName')
            ->willReturn('test');

        $this->subject->setRules($this->rules);
        $result = $this->subject->isGranted($request);
        $this->assertTrue($result);
    }

    /**
     * @throws Exception
     */
    public function testIsGranted(): void
    {
        $request     = $this->createMock(ServerRequest::class);
        $routeResult = $this->createMock(RouteResult::class);

        $request->expects($this->once())
            ->method('getAttribute')
            ->with(RouteResult::class)
            ->willReturn($routeResult);
        $routeResult->expects($this->atLeastOnce())
            ->method('getMatchedRouteName')
            ->willReturn('actions');

        $this->subject->setRules($this->rules);
        $result = $this->subject->isGranted($request);
        $this->assertTrue($result);
    }

    public static function rulesProvider(): array
    {
        return [
            'valid-placeholder'             => [
                [
                    'account-*'    => [
                        'view',
                        'create',
                    ],
                    'account-view' => [],
                ],
                'account-view',
                true,
            ],
            'invalid-placeholder'           => [
                [
                    'account-*' => [
                        'view',
                        'create',
                    ],
                ],
                'different-account-route',
                false,
            ],
            'valid-composite-placeholder'   => [
                [
                    'user-*-form' => [
                        'user-create-form',
                        'user-edit-form',
                    ],
                ],
                'user-create-form',
                true,
            ],
            'invalid-composite-placeholder' => [
                [
                    'user-*-form' => [
                        'user-create-form',
                        'user-edit-form',
                    ],
                ],
                'user-create-avatar',
                false,
            ],
        ];
    }

    #[DataProvider('rulesProvider')]
    public function testPlaceholderRoutes(array $rules, string $matchedRoute, bool $isValid): void
    {
        $request     = $this->createMock(ServerRequest::class);
        $routeResult = $this->createMock(RouteResult::class);

        $this->subject->setRules($rules);

        $request->expects($this->once())
            ->method('getAttribute')
            ->with(RouteResult::class)
            ->willReturn($routeResult);
        $routeResult->expects($this->atLeastOnce())
            ->method('getMatchedRouteName')
            ->willReturn($matchedRoute);

        $this->assertEquals($isValid, $this->subject->isGranted($request));
    }
}
