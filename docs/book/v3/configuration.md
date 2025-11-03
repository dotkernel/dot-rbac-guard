# Configuration

As with many Dotkernel modules, we focus on the configuration-based approach of customizing the module for your needs.

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
        
        //define which guard provider to use, along with its configuration
        //the guard provider should know how to build a list of GuardInterfaces based on its configuration
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
                                   //list of actions to apply, or empty array for all actions
                                   'actions' => [],
                                   //by default, authorization pass if all permissions are present (AND)
                                   //list of roles to allow
                                   'roles' => [],
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
                                    //list of actions to apply, or empty array for all actions
                                    'actions' => [],
                                    //by default, authorization pass if all permissions are present (AND)
                                    //list of permissions to allow
                                    'permissions' => [],
                                ],
                                [
                                    'route' => 'controller route name',
                                    //list of actions to apply, or empty array for all actions
                                    'actions' => [],
                                    'permissions' => [
                                        //permission can be defined in this way too, for all permission type guards
                                        //list of permissions
                                        'permissions' => [],
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
