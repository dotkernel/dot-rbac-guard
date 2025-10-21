# Configuration

As with many Dotkernel modules, we focus on the configuration based approach of customizing the module for your needs.

After installing, merge the module's `ConfigProvider` with your application's config to make sure required dependencies and default module configuration are registered.
Create a configuration file for this module in your 'config/autoload' folder.

## authorization-guards.global.php

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
                            ],
                        ],
                    ],
                    [
                        'type' => 'RoutePermission',
                        'options' => [
                            'rules' => [
                                'premium' => ['premium'],
                                'account' => ['my-account'],
                                'logout' => ['only-logged'],
                            ],
                        ],
                    ],
                    [
                        'type' => 'Controller',
                        'options' => [
                            'rules' => [
                                [
                                   'route' => 'controller route name',
                                   'actions' => [], //list of actions to apply, or empty array for all actions,
                                   // by default, authorization passes if all permissions are present(AND)
                                   'roles' => ['admin'], //list of roles to allow,
                               ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'ControllerPermission',
                        'options' => [
                            'rules' => [
                                [
                                    'route' => 'controller route name',
                                    'actions' => [], //list of actions to apply, or empty array for all actions,
                                    // by default, authorization passes if all permissions are present(AND)
                                    'permissions' => ['authenticated'], //list of permissions to allow,
                                ],
                                [
                                    'route' => 'controller route name',
                                    'actions' => [], //list of actions to apply, or empty array for all actions,
                                    'permissions' => [
                                        //permission can be defined in this way too, for all permission type guards
                                        'permissions' => ['authenticated'], //list of permissions,
                                        'condition' => \Dot\Rbac\Guard\GuardInterface::CONDITION_OR,
                                    ],
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

> It is **strongly recommended** to explicitly define permissions or roles and not leave the values empty, especially if using `GuardInterface::POLICY_DENY`!

## Route Name Placeholders

Route **names** are allowed to contain `*` as placeholders, allowing more compact specifications.
This feature is available for all types of guards.

> Note that route rules are verified in order of their writing, take care of the order when using placeholder routes, as not to overwrite any specific routes!

```php
[
    'type' => 'Route',
    'options' => [
        'rules' => [
            'account-create-form' => ['admin'],
            'account-update-form' => ['admin'],
            'account-delete-form' => ['admin'],
        ]
    ],
    [
        'type' => 'Controller',
        'options' => [
            'rules' => [
                [
                   'route' => 'admin-create',
                   'actions' => [],
                   'roles' => ['admin'],
               ],
               [
                   'route' => 'admin-edit',
                   'actions' => [],
                   'roles' => ['admin'],
               ],
               [
                   'route' => 'admin-view',
                   'actions' => [],
                   'roles' => ['admin'],
               ],
            ]
        ]
    ],
],


// Can be written as:

[
    'type' => 'Route',
    'options' => [
        'rules' => [
            'account-*-form' => ['admin'],
        ]
    ],
        [
        'type' => 'Controller',
        'options' => [
            'rules' => [
                [
                   'route' => 'admin-*',
                   'actions' => [],
                   'roles' => ['admin'],
               ],
            ]
        ]
    ],
]
```
