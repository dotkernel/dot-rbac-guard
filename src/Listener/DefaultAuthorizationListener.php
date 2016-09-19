<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/22/2016
 * Time: 4:25 PM
 */

namespace N3vrax\DkRbacGuard\Listener;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\Exception\UnauthorizedException;
use Dot\Authorization\Exception\ForbiddenException;
use Dot\Rbac\Guard\Event\AuthorizationEvent;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\GuardInterface;
use Dot\Rbac\Guard\GuardsProvider;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class DefaultAuthorizationListener extends AbstractListenerAggregate
{
    /** @var  GuardsProvider */
    protected $guardsProvider;

    /** @var  AuthenticationInterface */
    protected $authentication;

    public function __construct(GuardsProvider $guardsProvider, AuthenticationInterface $authentication)
    {
        $this->authentication = $authentication;
        $this->guardsProvider = $guardsProvider;
    }

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

    public function authorize(AuthorizationEvent $e)
    {
        $request = $e->getRequest();
        $response = $e->getResponse();

        $guards = $this->guardsProvider->getGuards();

        //iterate over guards, which are sorted by priority
        //break on the first one that does not grants access

        $isGranted = true;
        foreach ($guards as $guard)
        {
            if(!$guard instanceof GuardInterface) {
                throw new RuntimeException("Guard is not an instance of " . GuardInterface::class);
            }

            if(!$guard->isGranted($request, $response)) {
                $isGranted = false;
                break;
            }
        }

        $e->setAuthorized($isGranted);

    }

    public function authorizationPost(AuthorizationEvent $e)
    {
        $isGranted = $e->isAuthorized();
        if(!$isGranted)
        {
            //we throw a 401 if is guest, and let unauthorized exception handlers process it
            //403 otherwise, resulting in a final handler or redirect, whatever you register as the error handler
            if($this->authentication->hasIdentity())
            {
                throw new ForbiddenException(
                    'You don\'t have enough permissions to access this content', 403);
            }
            else
            {
                throw new UnauthorizedException(
                    'You must be authenticated to access this content', 401);
            }
        }
    }
}