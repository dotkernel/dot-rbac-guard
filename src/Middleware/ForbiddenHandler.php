<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 5/21/2016
 * Time: 9:23 PM
 */

namespace Dot\Rbac\Guard\Middleware;

use Dot\Rbac\Guard\Event\AuthorizationEvent;
use N3vrax\DkBase\Event\EventProviderTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ForbiddenHandler
{
    use EventProviderTrait;

    /** @var array  */
    protected $authorizationStatusCodes = [403];

    /**
     * @param $error
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     *
     * @return ResponseInterface
     */
    public function __invoke(
        $error,
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    )
    {
        if($error instanceof \Exception && in_array($error->getCode(), $this->authorizationStatusCodes)
            || in_array($response->getStatusCode(), $this->authorizationStatusCodes)
        )
        {
            $r = $this->triggerEvent(AuthorizationEvent::EVENT_FORBIDDEN, ['error' => $error], $request, $response);
            if($r instanceof ResponseInterface) {
                return $r;
            }

            //if no handler or not a response, use passthough strategy
            $response = $response->withStatus(403);
            if($error instanceof \Exception) {
                $error = $error->getMessage();
            }
        }

        return $next($request, $response, $error);
    }
}