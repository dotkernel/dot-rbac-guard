<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Middleware;

use Dot\Authorization\AuthorizationInterface;
use Dot\Authorization\Exception\ForbiddenException;
use Dot\Rbac\Guard\Middleware\ForbiddenHandler;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Exception;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ForbiddenHandlerTest extends TestCase
{
    protected AuthorizationInterface $mockAuthorizationInterface;
    protected RbacGuardOptions $rbacGuardsOptions;
    protected ForbiddenHandler $subject;

    public function setUp(): void
    {
        $this->mockAuthorizationInterface = new class implements AuthorizationInterface {
            public function isGranted(string $permission, array $roles = [], mixed $context = null): bool
            {
                return true;
            }
        };

        $this->rbacGuardsOptions = new RbacGuardOptions(null);

        $this->subject = new ForbiddenHandler($this->mockAuthorizationInterface, $this->rbacGuardsOptions);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testProcessForbiddenException()
    {
        $request = new ServerRequest();
        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                throw new ForbiddenException('ForbiddenException');
            }
        };

        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('ForbiddenException');
        $this->subject->process($request, $handler);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testProcessException()
    {
        $request = new ServerRequest();
        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                throw new Exception('Exception');
            }
        };

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Exception');
        $this->subject->process($request, $handler);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testProcess()
    {
        $request = new ServerRequest();
        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new TextResponse('All good');
            }
        };

        $result = $this->subject->process($request, $handler);
        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testIsDebug()
    {
        $this->subject->setDebug(true);
        $result = $this->subject->isDebug();
        $this->assertTrue($result);
    }
}
