# Register the middleware in the pipeline

The guards need the `RouteResult` request attribute to find the matched route and its parameters.
`Mezzio\Router\Middleware\RouteMiddleware` adds this attribute.
Thus, put the middleware of this package after `RouteMiddleware` and before `DispatchMiddleware`.

- `RbacGuardMiddleware` runs the guards. If access is denied, it throws `ForbiddenException` (403). If the container has a `Dot\Authentication\AuthenticationInterface` service and the user is a guest, it throws `UnauthorizedException` (401) instead.
- `ForbiddenHandler` catches `ForbiddenException` and each exception with the code 403. It renders the 403 template, or throws the exception again if there is no template renderer.

Put `ForbiddenHandler` before `RbacGuardMiddleware`, so that it catches the exceptions of the guards.

## config/pipeline.php

```php
use Dot\Rbac\Guard\Middleware\ForbiddenHandler;
use Dot\Rbac\Guard\Middleware\RbacGuardMiddleware;
use Mezzio\Router\Middleware\DispatchMiddleware;
use Mezzio\Router\Middleware\RouteMiddleware;

// ...

$app->pipe(RouteMiddleware::class);

// ...

$app->pipe(ForbiddenHandler::class);
$app->pipe(RbacGuardMiddleware::class);

// ...

$app->pipe(DispatchMiddleware::class);
```

## The 403 template

`ForbiddenHandler` uses the template `error::403`.
To use a different template, set `mezzio.error_handler.template_403` in your configuration:

```php
return [
    'mezzio' => [
        'error_handler' => [
            'template_403' => 'app::forbidden',
        ],
    ],
];
```

The handler sends these variables to the template: `request`, `uri`, `message`, `status` and `reason`.
When `debug` is `true`, it also sends `error`, which is the exception.
