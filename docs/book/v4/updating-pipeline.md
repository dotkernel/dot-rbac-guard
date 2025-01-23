# Register the RbacGuardMiddleware in the pipeline

The last step in order to use this package is to register the middleware.
This middleware triggers the authorization event.
You MUST insert this middleware between the routing middleware and the dispatch middleware of the application, because the guards need the `RouteResult` in order to get the matched route and params.

## middleware-pipeline.global.php

```php
//...

'routing' => [
    'middleware' => [
        ApplicationFactory::ROUTING_MIDDLEWARE,

        //...

        \Dot\Rbac\Guard\Middleware\RbacGuardMiddleware::class,

        //...

        ApplicationFactory::DISPATCH_MIDDLEWARE,
    ],
    'priority' => 1,
],

//...
```
