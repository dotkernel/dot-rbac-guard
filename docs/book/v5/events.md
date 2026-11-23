# Events

The middleware of the package triggers these events:

| Event | Constant | Trigger | Parameters |
| --- | --- | --- | --- |
| `event.beforeAuthorization` | `AuthorizationEvent::EVENT_BEFORE_AUTHORIZATION` | `RbacGuardMiddleware`, before the guards | `request`, `authorizationService` |
| `event.afterAuthorization` | `AuthorizationEvent::EVENT_AFTER_AUTHORIZATION` | `RbacGuardMiddleware`, after the guards | `request`, `authorizationService`, `authorized` |
| `event.authorization.forbidden` | `AuthorizationEvent::EVENT_FORBIDDEN` | `ForbiddenHandler`, for a 403 error | `request`, `authorizationService`, `error` |

A listener can:

- Return a `Psr\Http\Message\ResponseInterface`. The middleware stops and returns this response. For example, return a redirect to the login page.
- Change the `request` parameter. The value must stay a `ServerRequestInterface`, or the middleware throws `RuntimeException`.
- Change the `authorized` parameter in `event.afterAuthorization`, to grant or deny access.

## Write a listener

Extend `Dot\Rbac\Guard\Event\AbstractAuthorizationEventListener` and override the methods that you need:

```php
<?php

declare(strict_types=1);

namespace App\Listener;

use Dot\Rbac\Guard\Event\AbstractAuthorizationEventListener;
use Dot\Rbac\Guard\Event\AuthorizationEvent;

class AuditListener extends AbstractAuthorizationEventListener
{
    public function onAfterAuthorization(AuthorizationEvent $e): void
    {
        if ($e->getParam('authorized') !== true) {
            // write the denied request to your log
        }
    }
}
```

The methods of the listener return `void`.
To return a response, attach a callable directly to the event manager of the middleware:

```php
use Dot\Rbac\Guard\Event\AuthorizationEvent;
use Laminas\Diactoros\Response\RedirectResponse;

$middleware->getEventManager()->attach(
    AuthorizationEvent::EVENT_AFTER_AUTHORIZATION,
    static function (AuthorizationEvent $event): ?RedirectResponse {
        return $event->getParam('authorized') === true ? null : new RedirectResponse('/login');
    }
);
```

## Register a listener

Add the listener to `dot_authorization.event_listeners`.
An entry is a class or service name, or an array with the keys `type` and `priority`:

```php
'event_listeners' => [
    \App\Listener\AuditListener::class,
    [
        'type'     => \App\Listener\OtherListener::class,
        'priority' => 100,
    ],
],
```

If the container has a service with the name, the package uses that service.
Otherwise, it creates the class without arguments.
The listener must implement `Dot\Rbac\Guard\Event\AuthorizationEventListenerInterface`, or the factory throws `RuntimeException`.
A listener with a higher priority runs first.
