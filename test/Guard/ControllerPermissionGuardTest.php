<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Guard;

use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Guard\ControllerPermissionGuard;
use Laminas\Diactoros\ServerRequest;
use Mezzio\Router\RouteResult;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class ControllerPermissionGuardTest extends TestCase
{
    protected ControllerPermissionGuard $subject;

    protected AuthorizationInterface $mockAuthorizationInterface;

    protected array $rules = [
        [
            'route'       => 'account',
            'actions'     => [
                'avatar',
                'details',
                'changePassword',
                'deleteAccount',
                'index',
            ],
            'permissions' => ['*'],
        ],
        [
            'route'       => 'page',
            'actions'     => [
                'premium-content',
                'index',
            ],
            'permissions' => ['premium'],
        ],
        [
            'route'       => 'invalidRoute',
            'permissions' => [],
        ],
    ];

    public function setUp(): void
    {
        $this->mockAuthorizationInterface = new class implements AuthorizationInterface {
            public function isGranted(string $permission, array $roles = [], mixed $context = null): bool
            {
                return true;
            }
        };

        $this->subject = new ControllerPermissionGuard(
            [
                'authorization_service' => $this->mockAuthorizationInterface,
            ]
        );
    }

    public function testSetRules(): void
    {
        $this->subject->setRules($this->rules);
        $this->assertIsArray($this->subject->getRules());
    }

    public function testIsNotGrantedProtectionPolicy(): void
    {
        $request = new ServerRequest();
        $result  = $this->subject->isGranted($request);
        $this->assertFalse($result);
    }

    /**
     * @throws Exception
     */
    public function testIsNotGrantedRulesNotSet(): void
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
    public function testIsNotGrantedRulesInvalid(): void
    {
        $request     = $this->createMock(ServerRequest::class);
        $routeResult = $this->createMock(RouteResult::class);

        $request->expects($this->once())
            ->method('getAttribute')
            ->with(RouteResult::class)
            ->willReturn($routeResult);
        $routeResult->expects($this->once())
            ->method('getMatchedParams')
            ->willReturn([]);
        $routeResult->expects($this->atLeastOnce())
            ->method('getMatchedRouteName')
            ->willReturn('invalidroute');

        $this->subject->setRules($this->rules);

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
        $routeResult->expects($this->once())
            ->method('getMatchedParams')
            ->willReturn([]);
        $routeResult->expects($this->atLeastOnce())
            ->method('getMatchedRouteName')
            ->willReturn('account');

        $this->subject->setRules($this->rules);

        $result = $this->subject->isGranted($request);
        $this->assertTrue($result);
    }

    /**
     * @throws Exception
     */
    public function testIsGrantedPremium(): void
    {
        $request     = $this->createMock(ServerRequest::class);
        $routeResult = $this->createMock(RouteResult::class);

        $request->expects($this->once())
            ->method('getAttribute')
            ->with(RouteResult::class)
            ->willReturn($routeResult);
        $routeResult->expects($this->once())
            ->method('getMatchedParams')
            ->willReturn([]);
        $routeResult->expects($this->atLeastOnce())
            ->method('getMatchedRouteName')
            ->willReturn('page');

        $this->subject->setRules($this->rules);

        $result = $this->subject->isGranted($request);
        $this->assertTrue($result);
    }

    public function testSetAuthorizationService(): void
    {
        $this->subject->setAuthorizationService($this->mockAuthorizationInterface);
        $result = $this->subject->getAuthorizationService();
        $this->assertInstanceOf(AuthorizationInterface::class, $result);
    }

    public function rulesProvider(): array
    {
        return [
            'valid-placeholder'             => [
                [
                    [
                        'route'       => 'account-*',
                        'actions'     => [],
                        'permissions' => ['*'],
                    ],
                    [
                        'route'       => 'account-view',
                        'actions'     => [],
                        'permissions' => ['*'],
                    ],
                ],
                'account-view',
                true,
            ],
            'invalid-placeholder'           => [
                [
                    [
                        'route'       => 'account-*',
                        'actions'     => [],
                        'permissions' => ['*'],
                    ],
                ],
                'different-account-route',
                false,
            ],
            'valid-composite-placeholder'   => [
                [
                    [
                        'route'       => 'user-*-form',
                        'actions'     => [],
                        'permissions' => ['*'],
                    ],
                ],
                'user-create-form',
                true,
            ],
            'invalid-composite-placeholder' => [
                [
                    [
                        'route'       => 'user-*-form',
                        'actions'     => [],
                        'permissions' => ['*'],
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
        $routeResult->expects($this->any())
            ->method('getMatchedParams')
            ->willReturn([]);
        $routeResult->expects($this->atLeastOnce())
            ->method('getMatchedRouteName')
            ->willReturn($matchedRoute);

        $this->assertEquals($isValid, $this->subject->isGranted($request));
    }
}
