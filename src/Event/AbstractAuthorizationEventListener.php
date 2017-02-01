<?php
/**
 * @copyright: DotKernel
 * @library: dot-rbac-guard
 * @author: n3vrax
 * Date: 2/1/2017
 * Time: 10:07 PM
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Event;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

abstract class AbstractAuthorizationEventListener extends AbstractListenerAggregate implements
    AuthorizationEventListenerInterface
{
    /**
     * @param EventManagerInterface $events
     * @param int $priority
     * @param string $name
     */
    public function attach(EventManagerInterface $events, $priority = 1, string $name = '')
    {
        if (empty($name)) {
            return;
        }

        switch ($name) {
            case AuthorizationEvent::EVENT_AUTHORIZE:
                $this->listeners[] = $events->attach(
                    $name,
                    [$this, 'onAuthorize'],
                    $priority
                );
                break;

            case AuthorizationEvent::EVENT_FORBIDDEN:
                $this->listeners[] = $events->attach(
                    $name,
                    [$this, 'onForbidden'],
                    $priority
                );
                break;

            default:
                return;
        }
    }

    public function onAuthorize(AuthorizationEvent $e)
    {
        // NOOP: left for implementors
    }

    public function onForbidden(AuthorizationEvent $e)
    {
        // NOOP: left for implementors
    }
}
