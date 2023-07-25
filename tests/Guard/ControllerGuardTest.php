<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Guard;

use Dot\Rbac\Guard\Guard\ControllerGuard;
use Dot\Rbac\Role\RoleServiceInterface;
use Laminas\Diactoros\ServerRequest;
use Mezzio\Router\RouteResult;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class ControllerGuardTest extends TestCase
{
    protected ControllerGuard $subject;

    protected RoleServiceInterface $mockRoleServiceClass;

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
            'permissions' => ['unauthenticated'],
            'roles'       => ['*'],
        ],
        [
            'route'       => 'page',
            'actions'     => [
                'premium-content',
            ],
            'permissions' => ['premium'],
        ],
        [
            'route' => 'invalidRoute',
        ],
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
                return false;
            }
        };

        $this->subject = new ControllerGuard(
            [
                'protection_policy' => 'somePolicy',
                'role_service'      => $this->mockRoleServiceClass,
            ]
        );
    }

    public function testSetRules()
    {
        $this->subject->setRules($this->rules);
        $this->assertIsArray($this->subject->getRules());
    }

    public function testSetRoleService()
    {
        $this->subject->setRoleService($this->mockRoleServiceClass);
        $result = $this->subject->getRoleService();
        $this->assertInstanceOf(RoleServiceInterface::class, $result);
    }

    public function testIsNotGrantedProtectionPolicy()
    {
        $request = new ServerRequest();
        $result  = $this->subject->isGranted($request);
        $this->assertFalse($result);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testIsNotGrantedRulesNotSet()
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
     * @return void
     * @throws Exception
     */
    public function testIsNotGrantedRulesInvalid()
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
            ->willReturn('invalidRoute');

        $this->subject->setRules($this->rules);

        $result = $this->subject->isGranted($request);
        $this->assertFalse($result);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testIsGranted()
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
}
