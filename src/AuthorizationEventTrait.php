<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 9/20/2016
 * Time: 8:00 PM
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard;

use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Event\AuthorizationEvent;
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
     * @return AuthorizationEvent
     */
    protected function createAuthorizationEventWithError(
        AuthorizationInterface $authorization,
        mixed $error,
        string $name = AuthorizationEvent::EVENT_FORBIDDEN,
        array $eventParams = [],
        ServerRequestInterface $request = null
    ) : AuthorizationEvent {

        $event = $this->createAuthorizationEvent($authorization, $name, $eventParams, $request);
        $event->setError($error);

        return $event;
    }

    /**
     * @param AuthorizationInterface $authorization
     * @param string $name
     * @param array $eventParams
     * @param ServerRequestInterface|null $request
     * @return AuthorizationEvent
     */
    protected function createAuthorizationEvent(
        AuthorizationInterface $authorization,
        string $name = AuthorizationEvent::EVENT_AUTHORIZE,
        array $eventParams = [],
        ServerRequestInterface $request = null
    ) : AuthorizationEvent {
        $event = new AuthorizationEvent();
        $event->setName($name);
        $event->setTarget($this);
        $event->setAuthorizationService($authorization);

        if ($request) {
            $event->setRequest($request);
        }

        $event->setParams(array_merge($event->getParams(), $eventParams));

        return $event;
    }
}
