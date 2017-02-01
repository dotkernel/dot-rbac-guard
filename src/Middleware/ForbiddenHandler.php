<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 9:23 PM
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Middleware;

use Dot\Authorization\AuthorizationInterface;
use Dot\Authorization\Exception\ForbiddenException;
use Dot\Rbac\Guard\AuthorizationEventTrait;
use Dot\Rbac\Guard\Event\AuthorizationEvent;
use Dot\Rbac\Guard\Event\AuthorizationEventListenerAwareInterface;
use Dot\Rbac\Guard\Event\AuthorizationEventListenerAwareTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ForbiddenHandler
 * @package Dot\Rbac\Guard\Middleware
 */
class ForbiddenHandler implements AuthorizationEventListenerAwareInterface
{
    use AuthorizationEventListenerAwareTrait;
    use AuthorizationEventTrait;

    /** @var  AuthorizationInterface */
    protected $authorizationService;

    /** @var array */
    protected $authorizationStatusCodes = [403];

    /**
     * ForbiddenHandler constructor.
     * @param AuthorizationInterface $authorizationService
     */
    public function __construct(AuthorizationInterface $authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }

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
    ): ResponseInterface {
        //check for forbidden errors
        if ($error instanceof \Exception && in_array($error->getCode(), $this->authorizationStatusCodes)
            || in_array($response->getStatusCode(), $this->authorizationStatusCodes)
        ) {
            $event = $this->createAuthorizationEventWithError(
                $this->authorizationService,
                $error,
                AuthorizationEvent::EVENT_FORBIDDEN,
                [],
                $request
            );

            $result = $this->getEventManager()->triggerEventUntil(function ($r) {
                return ($r instanceof ResponseInterface);
            }, $event);

            $result = $result->last();

            if ($result instanceof ResponseInterface) {
                return $result;
            }

            //if no handler or not a response, use pass-trough strategy
            $response = $response->withStatus(403);
            //if we use pass-through, convert the exception into a regular string error, to avoid whoops
            //only if the exception is of the right type
            if ($error instanceof ForbiddenException) {
                $error = $error->getMessage();
            }
        }

        return $next($request, $response, $error);
    }
}
