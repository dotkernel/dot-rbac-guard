<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Middleware;

use Dot\Authorization\AuthorizationInterface;
use Dot\Authorization\Exception\ForbiddenException;
use Dot\Rbac\Guard\Event\AuthorizationEvent;
use Dot\Rbac\Guard\Event\AuthorizationEventListenerInterface;
use Dot\Rbac\Guard\Event\AuthorizationEventListenerTrait;
use Dot\Rbac\Guard\Event\DispatchAuthorizationEventTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ForbiddenHandler
 * @package Dot\Rbac\Guard\Middleware
 */
class ForbiddenHandler implements AuthorizationEventListenerInterface
{
    use DispatchAuthorizationEventTrait;
    use AuthorizationEventListenerTrait;

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
            $event = $this->dispatchEvent(AuthorizationEvent::EVENT_FORBIDDEN, [
                'request' => $request,
                'authorizationService' => $this->authorizationService,
                'error' => $error
            ]);
            if ($event instanceof ResponseInterface) {
                return $event;
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
