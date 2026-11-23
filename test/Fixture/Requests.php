<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Fixture;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use Mezzio\Router\Route;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Builds requests with a real RouteResult. RouteResult is final in mezzio-router 4, so it cannot be mocked.
 */
final class Requests
{
    /**
     * @param array<string, mixed> $params
     */
    public static function forRoute(string $routeName, array $params = []): ServerRequestInterface
    {
        $middleware = new class implements MiddlewareInterface {
            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ): ResponseInterface {
                return new Response();
            }
        };

        $routeResult = RouteResult::fromRoute(new Route('/test', $middleware, null, $routeName), $params);

        return (new ServerRequest())->withAttribute(RouteResult::class, $routeResult);
    }

    public static function forRouteFailure(): ServerRequestInterface
    {
        return (new ServerRequest())->withAttribute(RouteResult::class, RouteResult::fromRouteFailure(null));
    }

    public static function withoutRouteResult(): ServerRequestInterface
    {
        return new ServerRequest();
    }
}
