<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 2:09 AM
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Middleware;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\Exception\UnauthorizedException;
use Dot\Authorization\AuthorizationInterface;
use Dot\Authorization\Exception\ForbiddenException;
use Dot\Rbac\Guard\Event\AuthorizationEvent;
use Dot\Rbac\Guard\Event\AuthorizationEventListenerInterface;
use Dot\Rbac\Guard\Event\AuthorizationEventListenerTrait;
use Dot\Rbac\Guard\Event\DispatchAuthorizationEventTrait;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Guard\GuardInterface;
use Dot\Rbac\Guard\Options\MessagesOptions;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Guard\Provider\GuardsProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouteResult;

/**
 * Class RbacGuardMiddleware
 * @package Dot\Rbac\Guard\Middleware
 */
class RbacGuardMiddleware implements AuthorizationEventListenerInterface
{
    use DispatchAuthorizationEventTrait;
    use AuthorizationEventListenerTrait;

    /** @var AuthorizationInterface */
    protected $authorizationService;

    /** @var  RbacGuardOptions */
    protected $options;

    /** @var  GuardsProviderInterface */
    protected $guardsProvider;

    /** @var  AuthenticationInterface */
    protected $authentication;

    /**
     * RbacGuardMiddleware constructor.
     * @param AuthorizationInterface $authorizationService
     * @param GuardsProviderInterface $guardsProvider
     * @param RbacGuardOptions $options
     * @param AuthenticationInterface|null $authentication
     */
    public function __construct(
        AuthorizationInterface $authorizationService,
        GuardsProviderInterface $guardsProvider,
        RbacGuardOptions $options,
        AuthenticationInterface $authentication = null
    ) {
        $this->authorizationService = $authorizationService;
        $this->guardsProvider = $guardsProvider;
        $this->options = $options;
        $this->authentication = $authentication;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return ResponseInterface
     * @throws ForbiddenException
     * @throws UnauthorizedException
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ): ResponseInterface {
        $event = $this->dispatchEvent(AuthorizationEvent::EVENT_BEFORE_AUTHORIZATION, [
            'request' => $request,
            'authorizationService' => $this->authorizationService
        ]);
        if ($event instanceof ResponseInterface) {
            return $event;
        }

        $routeResult = $request->getAttribute(RouteResult::class, null);
        if ($routeResult instanceof RouteResult) {
            $guards = $this->guardsProvider->getGuards();

            //iterate over guards, which are sorted by priority
            //break on the first one that does not grants access

            $isGranted = $this->options->getProtectionPolicy() === GuardInterface::POLICY_ALLOW;
            foreach ($guards as $guard) {
                if (!$guard instanceof GuardInterface) {
                    throw new RuntimeException("Guard is not an instance of " . GuardInterface::class);
                }

                //according to the policy, we whitelist or blacklist matched routes
                $r = $guard->isGranted($request);
                if ($r !== $isGranted) {
                    $isGranted = $r;
                    break;
                }
            }
            $event->setParam('authorized', $isGranted);
        } else {
            $event->setParam('authorized', true);
        }

        $params = $event->getParams();
        $event = $this->dispatchEvent(AuthorizationEvent::EVENT_AFTER_AUTHORIZATION, $params);
        if ($event instanceof ResponseInterface) {
            return $event;
        }

        $isGranted = $event->getParam('authorized', true);
        if (!$isGranted) {
            if ($this->authentication) {
                //we throw a 401 if is guest, and let unauthorized exception handlers process it
                //403 otherwise, resulting in a final handler or redirect, whatever you register as the error handler
                if (!$this->authentication->hasIdentity()) {
                    throw new UnauthorizedException(
                        $this->options->getMessagesOptions()->getMessage(MessagesOptions::UNAUTHORIZED),
                        401
                    );
                }
            }

            throw new ForbiddenException(
                $this->options->getMessagesOptions()->getMessage(MessagesOptions::FORBIDDEN),
                403
            );
        }

        return $next($request, $response);
    }
}
