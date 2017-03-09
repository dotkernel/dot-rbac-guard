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
return [
    'dot_authorization' => [
    
        //define how it will treat non-matching guard rules, allow all by default
        'protection_policy' => \Dot\Rbac\Guard\GuardInterface::POLICY_ALLOW,
        
        'event_listeners' => [
            [
                'type' => 'class or service name of the listener',
                'priority' => 1,
            ],
        ],
        
        //define custom guards here
        'guard_manager' => [],
        
        //register custom guards providers here
        'guards_provider_manager' => [],
        
        //define which guards provider to use, along with its configuration
        //the guards provider should know how to build a list of GuardInterfaces based on its configuration
        'guards_provider' => [
            'type' => 'ArrayGuards',
            'options' => [
                'guards' => [
                    [
                        'type' => 'Route',
                        'options' => [
                            'rules' => [
                                'premium' => ['admin'],
                                'login' => ['guest'],
                                'logout' => ['admin', 'user', 'viewer'],
                                'account' => ['admin', 'user'],
                                'home' => ['*'],
                            ]
                        ]
                    ],
                    [
                        'type' => 'RoutePermission',
                        'options' => [
                            'rules' => [
                                'premium' => ['premium'],
                                'account' => ['my-account'],
                                'logout' => ['only-logged'],
                            ]
                        ]
                    ],
                    [
                        'type' => 'Controller',
                        'options' => [
                            'rules' => [
                                [
                                   'route' => 'controller route name',
                                   'actions' => [//list of actions to apply, or empty array for all actions],
                                   //by default, authorization pass if all permissions are present(AND)
                                   'roles' => [//list of roles to allow],
                               ],
                            ]
                        ]
                    ],
                    [
                        'type' => 'ControllerPermission',
                        'options' => [
                            'rules' => [
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
                            ]
                        ]
                    ]
                ]
            ],
        ],

        //overwrite default messages
        'messages_options' => [
            'messages' => [
                //MessagesOptions::UNAUTHORIZED => 'You must sign in first to access the requested content',
                //MessagesOptions::FORBIDDEN => 'You don\'t have enough permissions to access the requested content',
            ]
        ],
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

@TODO: more documentation
