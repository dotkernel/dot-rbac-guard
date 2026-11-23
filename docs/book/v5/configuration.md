# Configuration

The configuration of the package is in the `dot_authorization` key.
The `ConfigProvider` of the package gives the default values.
Create the file `config/autoload/authorization-guards.global.php` to change them.

## authorization-guards.global.php

```php
<?php

declare(strict_types=1);

use Dot\Rbac\Guard\Guard\GuardInterface;
use Dot\Rbac\Guard\Options\MessagesOptions;

return [
    'dot_authorization' => [
        // what a guard does when no rule matches the route: POLICY_ALLOW (default) or POLICY_DENY
        'protection_policy' => GuardInterface::POLICY_ALLOW,

        // listeners for the authorization events, see the "Events" page
        'event_listeners' => [
            [
                'type'     => 'class or service name of the listener',
                'priority' => 1,
            ],
        ],

        // add your own guards here, see "Custom guards and guards providers"
        'guard_manager' => [],

        // add your own guards providers here
        'guards_provider_manager' => [],

        // the guards provider and its configuration
        // the guards provider builds the list of guards
        'guards_provider' => [
            'type'    => 'ArrayGuards',
            'options' => [
                'guards' => [
                    [
                        'type'    => 'Route',
                        'options' => [
                            'rules' => [
                                'premium' => ['admin'],
                                'login'   => ['guest'],
                                'logout'  => ['admin', 'user', 'viewer'],
                                'account' => ['admin', 'user'],
                                'home'    => ['*'],
                            ],
                        ],
                    ],
                    [
                        'type'    => 'RoutePermission',
                        'options' => [
                            'rules' => [
                                'premium' => ['premium'],
                                'account' => ['my-account'],
                                'logout'  => ['only-logged'],
                            ],
                        ],
                    ],
                    [
                        'type'    => 'Controller',
                        'options' => [
                            'rules' => [
                                [
                                    'route'   => 'controller route name',
                                    // the actions of the rule, or an empty array for all actions
                                    'actions' => [],
                                    // the roles that get access
                                    'roles'   => ['admin'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'type'    => 'ControllerPermission',
                        'options' => [
                            'rules' => [
                                [
                                    'route'       => 'controller route name',
                                    'actions'     => [],
                                    // by default, the user needs all the permissions (AND)
                                    'permissions' => ['authenticated'],
                                ],
                                [
                                    'route'       => 'other controller route name',
                                    'actions'     => [],
                                    // this format is available in the two permission guards
                                    'permissions' => [
                                        'permissions' => ['authenticated', 'premium'],
                                        'condition'   => GuardInterface::CONDITION_OR,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],

        // change the default messages
        'messages_options' => [
            'messages' => [
                MessagesOptions::UNAUTHORIZED => 'You must sign in first to access the requested content',
                MessagesOptions::FORBIDDEN    => 'You do not have enough permissions to access the requested content',
            ],
        ],
    ],
];
```

## The guards

| Type | Priority | Rule | Access is granted when |
| --- | --- | --- | --- |
| `Route` | 100 | Route name and roles | The user has one of the roles |
| `RoutePermission` | 70 | Route name and permissions | The user has the permissions (AND or OR) |
| `Controller` | 40 | Route name, actions and roles | The user has one of the roles |
| `ControllerPermission` | 10 | Route name, actions and permissions | The user has the permissions (AND or OR) |

- The role or permission `*` gives access to all users.
- The route names are not case-sensitive.
- The controller guards get the action from the `action` route parameter. If there is no `action` parameter, the action is `index`. An action such as `change-password` matches the rule action `change-password` and `changePassword`.
- A permission rule with no permissions applies the protection policy.
- A role rule with no roles denies access.

> Always give roles or permissions to a rule. This is very important when you use `GuardInterface::POLICY_DENY`.

## How the middleware uses the guards

`RbacGuardMiddleware` calls the guards in the order of their priority, highest first.
It stops at the first guard whose result is different from the protection policy, and uses that result.

| Protection policy | Result |
| --- | --- |
| `POLICY_ALLOW` | Access is denied if one guard denies it |
| `POLICY_DENY` | Access is granted if one guard grants it |

Thus, with `POLICY_DENY`, one guard that grants access is sufficient, even if a different guard denies access.

If the request has no `RouteResult`, the middleware grants access.
If the request has a `RouteResult` but no route matched (for example, a 404), each guard applies the protection policy.

## Route name placeholders

A rule can use `*` as a placeholder in the route name.
This is available in all the guards.

- `*` matches one or more letters, digits, `_` or `-`.
- `*` does not match `.`, `/`, `:` or other characters. For example, `admin.*` matches `admin.list`, but not `admin.user.list`.
- All other characters in the rule must be the same in the route name. For example, `.` is a dot, not "any character".

The guard uses the first rule that matches, in the order of writing.
Put the specific rules before the rules with placeholders.

```php
[
    'type'    => 'Route',
    'options' => [
        'rules' => [
            'account-create-form' => ['admin'],
            'account-update-form' => ['admin'],
            'account-delete-form' => ['admin'],
        ],
    ],
],
[
    'type'    => 'Controller',
    'options' => [
        'rules' => [
            ['route' => 'admin-create', 'actions' => [], 'roles' => ['admin']],
            ['route' => 'admin-edit', 'actions' => [], 'roles' => ['admin']],
            ['route' => 'admin-view', 'actions' => [], 'roles' => ['admin']],
        ],
    ],
],
```

You can write the same rules as:

```php
[
    'type'    => 'Route',
    'options' => [
        'rules' => [
            'account-*-form' => ['admin'],
        ],
    ],
],
[
    'type'    => 'Controller',
    'options' => [
        'rules' => [
            ['route' => 'admin-*', 'actions' => [], 'roles' => ['admin']],
        ],
    ],
],
```

## Custom guards and guards providers

`guard_manager` and `guards_provider_manager` accept two keys:

- `factories`: a map of a name to a factory. A factory is a callable or the name of an invokable class. It gets the container, the name and the `options` of the guard config.
- `aliases`: a map of an alias to a name.

A guard must implement `Dot\Rbac\Guard\Guard\GuardInterface`.
A guards provider must implement `Dot\Rbac\Guard\Provider\GuardsProviderInterface`.

```php
'guard_manager' => [
    'factories' => [
        IpGuard::class => IpGuardFactory::class,
    ],
    'aliases'   => [
        'Ip' => IpGuard::class,
    ],
],
```

Then use `'type' => 'Ip'` in the `guards` list.

Use a factory class, not a closure, if you cache your configuration.
The plugin managers do not support other keys, such as `invokables` or `delegators`.
