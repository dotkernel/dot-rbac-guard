<?php

declare(strict_types=1);

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
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RbacGuardMiddleware implements MiddlewareInterface, AuthorizationEventListenerInterface
{
    use AuthorizationEventListenerTrait;
    use DispatchAuthorizationEventTrait;

    protected AuthorizationInterface $authorizationService;

    protected RbacGuardOptions $options;

    protected GuardsProviderInterface $guardsProvider;

    protected ?AuthenticationInterface $authentication;

    public function __construct(
        AuthorizationInterface $authorizationService,
        GuardsProviderInterface $guardsProvider,
        RbacGuardOptions $options,
        ?AuthenticationInterface $authentication = null
    ) {
        $this->authorizationService = $authorizationService;
        $this->guardsProvider       = $guardsProvider;
        $this->options              = $options;
        $this->authentication       = $authentication;
    }

    /**
     * @throws ForbiddenException
     * @throws UnauthorizedException
     * @throws RuntimeException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $event = $this->dispatchEvent(AuthorizationEvent::EVENT_BEFORE_AUTHORIZATION, [
            'request'              => $request,
            'authorizationService' => $this->authorizationService,
        ]);
        if ($event instanceof ResponseInterface) {
            return $event;
        }

        $request     = $this->getRequestParam($event);
        $routeResult = $request->getAttribute(RouteResult::class);
        if ($routeResult instanceof RouteResult) {
            // the guards are sorted by priority
            // stop at the first guard that does not agree with the protection policy
            $isGranted = $this->options->getProtectionPolicy() === GuardInterface::POLICY_ALLOW;
            foreach ($this->guardsProvider->getGuards() as $guard) {
                if (! $guard instanceof GuardInterface) {
                    throw new RuntimeException('Guard is not an instance of ' . GuardInterface::class);
                }

                $result = $guard->isGranted($request);
                if ($result !== $isGranted) {
                    $isGranted = $result;
                    break;
                }
            }
            $event->setParam('authorized', $isGranted);
        } else {
            $event->setParam('authorized', true);
        }

        $event = $this->dispatchEvent(AuthorizationEvent::EVENT_AFTER_AUTHORIZATION, $event->getParams());
        if ($event instanceof ResponseInterface) {
            return $event;
        }

        $request = $this->getRequestParam($event);
        if (! $event->getParam('authorized', true)) {
            // a guest gets a 401, so that the unauthorized exception handlers can process it
            // an identity gets a 403, which goes to the error handler that you register
            if ($this->authentication !== null && ! $this->authentication->hasIdentity()) {
                throw new UnauthorizedException(
                    $this->options->getMessagesOptions()->getMessage(MessagesOptions::UNAUTHORIZED),
                    401
                );
            }

            throw new ForbiddenException(
                $this->options->getMessagesOptions()->getMessage(MessagesOptions::FORBIDDEN),
                403
            );
        }

        return $handler->handle($request);
    }

    /**
     * @param AuthorizationEvent<object|string, array<string, mixed>> $event
     * @throws RuntimeException
     */
    private function getRequestParam(AuthorizationEvent $event): ServerRequestInterface
    {
        $request = $event->getParam('request');
        if (! $request instanceof ServerRequestInterface) {
            throw new RuntimeException(
                'The "request" parameter of the authorization event must be an instance of '
                . ServerRequestInterface::class
            );
        }

        return $request;
    }
}
