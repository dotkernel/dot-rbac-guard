<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Guard;

use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Guard\RoutePermissionGuard;
use Laminas\Diactoros\ServerRequest;
use Mezzio\Router\RouteResult;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class RoutePermissionGuardTest extends TestCase
{
    protected RoutePermissionGuard $subject;

    protected AuthorizationInterface $mockAuthorizationInterface;

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
        $this->mockAuthorizationInterface = new class implements AuthorizationInterface {
            public function isGranted(string $permission, array $roles = [], mixed $context = null): bool
            {
                return true;
            }
        };

        $this->subject = new RoutePermissionGuard(
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
}
