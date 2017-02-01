<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 6/22/2016
 * Time: 4:25 PM
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Listener;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\Exception\UnauthorizedException;
use Dot\Authorization\Exception\ForbiddenException;
use Dot\Rbac\Guard\Event\AuthorizationEvent;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Guard\GuardInterface;
use Dot\Rbac\Guard\Options\MessagesOptions;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Guard\Provider\GuardsProviderInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Expressive\Router\RouteResult;

/**
 * Class DefaultAuthorizationListener
 * @package Dot\Rbac\Guard\Listener
 */
class DefaultAuthorizationListener extends AbstractListenerAggregate
{
    /** @var  RbacGuardOptions */
    protected $options;

    /** @var  GuardsProviderInterface */
    protected $guardsProvider;

    /** @var  AuthenticationInterface */
    protected $authentication;

    /**
     * DefaultAuthorizationListener constructor.
     * @param GuardsProviderInterface $guardsProvider
     * @param RbacGuardOptions $options
     * @param AuthenticationInterface|null $authentication
     */
    public function __construct(
        RbacGuardOptions $options,
        GuardsProviderInterface $guardsProvider,
        AuthenticationInterface $authentication = null
    ) {
        $this->authentication = $authentication;
        $this->options = $options;
        $this->guardsProvider = $guardsProvider;
    }

    /**
     * @param EventManagerInterface $events
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            AuthorizationEvent::EVENT_AUTHORIZE,
            [$this, 'authorize'],
            1
        );

        $this->listeners[] = $events->attach(
            AuthorizationEvent::EVENT_AUTHORIZE,
            [$this, 'authorizationPost'],
            -1000
        );
    }

    /**
     * @param AuthorizationEvent $e
     */
    public function authorize(AuthorizationEvent $e)
    {
        $request = $e->getRequest();

        //if no route result(a.k.a 404) authorize it and let it go to the final handler
        $routeResult = $request->getAttribute(RouteResult::class, null);
        if (!$routeResult instanceof RouteResult) {
            $e->setAuthorized(true);
            return;
        }

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

        $e->setAuthorized($isGranted);
    }

    /**
     * @param AuthorizationEvent $e
     * @throws ForbiddenException
     * @throws UnauthorizedException
     */
    public function authorizationPost(AuthorizationEvent $e)
    {
        $isGranted = $e->isAuthorized();
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
    }
}
