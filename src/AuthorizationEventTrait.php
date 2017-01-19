<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 9/20/2016
 * Time: 8:00 PM
 */

namespace Dot\Rbac\Guard;

use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Event\AuthorizationEvent;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class AuthorizationEventTrait
 * @package Dot\Rbac\Guard
 */
trait AuthorizationEventTrait
{
    /**
     * @param AuthorizationInterface $authorization
     * @param $error
     * @param string $name
     * @param array $eventParams
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return AuthorizationEvent
     */
    protected function createAuthorizationEventWithError(
        AuthorizationInterface $authorization,
        $error,
        $name = AuthorizationEvent::EVENT_FORBIDDEN,
        array $eventParams = [],
        ServerRequestInterface $request = null,
        ResponseInterface $response = null
    ) {

        $event = $this->createAuthorizationEvent($authorization, $name, $eventParams, $request, $response);
        $event->setError($error);

        return $event;
    }

    /**
     * @param AuthorizationInterface $authorization
     * @param string $name
     * @param array $eventParams
     * @param ServerRequestInterface|null $request
     * @param ResponseInterface|null $response
     * @return AuthorizationEvent
     */
    protected function createAuthorizationEvent(
        AuthorizationInterface $authorization,
        $name = AuthorizationEvent::EVENT_AUTHORIZE,
        array $eventParams = [],
        ServerRequestInterface $request = null,
        ResponseInterface $response = null
    ) {
        $event = new AuthorizationEvent();
        $event->setName($name);
        $event->setTarget($this);
        $event->setAuthorizationService($authorization);

        if ($request) {
            $event->setRequest($request);
        }

        if ($response) {
            $event->setResponse($response);
        }

        $event->setParams(array_merge($event->getParams(), $eventParams));

        return $event;
    }
}
