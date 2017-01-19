# dot-rbac-guard

Defines authorization guards that authorize users to certain parts of an application based on various criteria.
If the authorization service can be used to check authorization on a narrow level, the guards are meant to work as gateways to bigger parts of an application.
Usually, you'll want to use both methods in an application for additional security.

## Installation

Run the following command in your project's root directory
```bash
$ composer require dotkernel/dot-rbac-guard
```

Please note that this module is built around the authorization service defined in module dot-rbac. 
Running the above command will also install that package. You'll have to first configure dot-rbac before using this module.

## Configuration

As with many DotKernel modules, we focus on the configuration based approach of customizing the module for your needs.

After installing, make sure you merge the module's `ConfigProvider` with your application's config to make sure required dependencies and default module configuration is registered.
Create a configuration file for this module in your 'config/autoload' folder

##### authorization-guards.global.php
```php
'dot_authorization' => [

    //define how it will treat non-matching guard rules, allow all by default
    'protection_policy' => \Dot\Rbac\Guard\GuardInterface::POLICY_ALLOW,

    //register custom guards providers here
    'guards_provider_manager' => [],

    //define custom guards here
    'guard_manager' => [],

    //define which guards provider to use, along with its configuration
    //the guards provider should know how to build a list of GuardInterfaces based on its configuration
    'guards_provider' => [

        //the list of guards to use. Custom guards need to be registered in the guard manager first
        \Dot\Rbac\Guard\Provider\ArrayGuardsProvider::class => [

            //the RouteGuard allows you to restrict access to routes based on the user's role
            //to block access to a route, set the roles to an empty array
            \Dot\Rbac\Guard\Route\RouteGuard::class => [
                'premium' => ['admin'],
                'login' => ['guest'],
                'logout' => ['admin', 'user', 'viewer'],
                'account' => ['admin', 'user'],
                'home' => ['*'],
            ],
            //the RoutePermissionGuard allows you to restrict access to routes based on permissions
            \Dot\Rbac\Guard\Route\RoutePermissionGuard::class => [
                'premium' => ['premium'],
                'account' => ['my-account'],
                'logout' => ['only-logged'],
            ],

            \Dot\Rbac\Guard\Controller\ControllerGuard::class => [
               [
                   'route' => 'controller route name',
                   'actions' => [//list of actions to apply, or empty array for all actions],
                   //by default, authorization pass if all permissions are present(AND)
                   'roles' => [//list of roles to allow],
               ], 
            ],

            \Dot\Rbac\Guard\Controller\ControllerPermissionGuard::class => [
                [
                    'route' => 'controller route name',
                    'actions' => [//list of actions to apply, or empty array for all actions],
                    //by default, authorization pass if all permissions are present(AND)
                    'permissions' => [//list of permissions to allow],
                ],
                [
                    'route' => 'controller route name',
                    'actions' => [//list of actions to apply, or empty array for all actions],
                    'permissions' => [
                        //permission can be defined in this way too, for all permission type guards
                        'permissions' => [//list of permissions],
                        'condition' => \Dot\Rbac\Guard\GuardInterface::CONDITION_OR,
                    ]
                ]
            ],

        ],
    ],

    //enable wanted url appending, used in the redirect on forbidden handler for now
    'allow_redirect_param' => true,

    //the name of the query param appended for the wanted url
    'redirect_query_name' => 'redirect'

    //these options apply only if the RedirectForbiddenHandler is active
    //this handler redirect to a preset route on forbidden errors
    'redirect_options' => [

        'enable' => false,

        //options for the redirect on forbidden handler
        'redirect_options' => [

            'enable' => false,

            'redirect_route' => [
                'name' => '',
                'params' => []
            ],
        ],
    ],

    //overwrite default messages
    //'messages_options' => [
        'messages' => [
            //MessagesOptions::UNAUTHORIZED_MESSAGE => 'You must be authenticated to access the requested content',
            //MessagesOptions::FORBIDDEN_MESSAGE => 'You don\'t have enough permissions to access the requested content',
        ]
    ],
];
```

## Register the RbacGuardMiddleware in the pipe

The last step in order to use this package is to register the middleware. This middleware triggers the authorization event.
You MUST insert this middleware between the routing middleware and the dispatch middleware of the application, because the guards need the RouteResult in order to get the matched route and params.

##### middleware-pipeline.global.php
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

Authorization through guards is event based. We provide a default event listener that does the job of asking each registered guard if permission is allowed based on rules set in the configuration.
By using this approach, you can further customize authorization, by registering your event listeners before or after the actual authorization.

When authorization fails, there are 2 possible outcomes
* a UnauthorizedException is triggered(dot-authentication) with status code 401 only if there is no authenticated identity(guest). This basically delegates the responsibility to the authentication error handlers.
Usually it will redirect to the login page in order to let users authenticate.
* a ForbiddenException is throw otherwise. By default the error is passed to the final error handler of the application with a status code 403.
There is also a redirect strategy error handler, which redirect instead to a predefined route.

## Authorization events

An authorization event is represented by the class `AuthorizationEvent` which contains the authorization result, the authorization service and any errors(messages or objects that).
The possible event names that you can listen are

```php
AuthorizationEvent::EVENT_AUTHORIZE
```
Triggered by the guards middleware, in order to initiate the authorization process through guards

```php
AuthorizationEvent::EVENT_FORBIDDEN
```
Triggered if authorization fails, if one or more guards do not allow request to pass.
