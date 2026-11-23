<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Fixture;

use Closure;
use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Returns a text response, or calls the given closure. Records the last request it received.
 */
final class RequestHandler implements RequestHandlerInterface
{
    public ?ServerRequestInterface $request = null;

    /**
     * @param (Closure(ServerRequestInterface): ResponseInterface)|null $callback
     */
    public function __construct(private readonly ?Closure $callback = null)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->request = $request;
        if ($this->callback !== null) {
            return ($this->callback)($request);
        }

        return new TextResponse('handled');
    }
}
