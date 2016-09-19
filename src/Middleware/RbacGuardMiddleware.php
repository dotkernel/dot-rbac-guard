<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 5/21/2016
 * Time: 2:09 AM
 */

namespace Dot\Rbac\Guard\Middleware;


use Dot\Rbac\Guard\Event\AuthorizationEvent;
use N3vrax\DkBase\Event\EventProviderTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RbacGuardMiddleware
{
    use EventProviderTrait;

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $r = $this->triggerEvent(AuthorizationEvent::EVENT_AUTHORIZE, [], $request, $response);
        if($r instanceof ResponseInterface) {
            return $r;
        }

        return $next($request, $response);
    }
}