<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 2:09 AM
 */

namespace Dot\Rbac\Guard\Middleware;


use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\AuthorizationEventTrait;
use Dot\Rbac\Guard\Event\AuthorizationEvent;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * Class RbacGuardMiddleware
 * @package Dot\Rbac\Guard\Middleware
 */
class RbacGuardMiddleware
{
    use EventManagerAwareTrait;
    use AuthorizationEventTrait;

    /** @var AuthorizationInterface  */
    protected $authorizationService;

    /**
     * RbacGuardMiddleware constructor.
     * @param AuthorizationInterface $authorizationService
     */
    public function __construct(AuthorizationInterface $authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $event = $this->createAuthorizeEvent(
            $this->authorizationService,
            AuthorizationEvent::EVENT_AUTHORIZE,
            [], $request, $response);

        $result = $this->getEventManager()->triggerEventUntil(function($r){
            return ($r instanceof ResponseInterface);
        }, $event);

        $result = $result->last();
        if($result instanceof ResponseInterface) {
            return $result;
        }

        return $next($request, $response);
    }
}