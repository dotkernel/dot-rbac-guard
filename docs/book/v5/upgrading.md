# Upgrading

This page lists the changes in version 5 that can break your code.
For each change, it gives the symptom and the fix.

## Upgrade from version 4

### Requirements

| Change | Symptom | Fix |
| --- | --- | --- |
| PHP 8.2 support stops | Composer cannot install version 5 | Use PHP 8.3, 8.4, 8.5 or 8.6 |
| The package does not require `laminas/laminas-servicemanager` | `Class "Laminas\ServiceManager\..." not found` in your code | Add `laminas/laminas-servicemanager` to the `composer.json` of your application |
| The package does not require `dotkernel/dot-controller` | `Class "Dot\Controller\..." not found` in your code | Add `dotkernel/dot-controller` to the `composer.json` of your application if your code uses it |

### Plugin managers

| Change | Symptom | Fix |
| --- | --- | --- |
| `GuardPluginManager` and `GuardsProviderPluginManager` do not extend `Laminas\ServiceManager\AbstractPluginManager` | A call to `setService()`, `configure()`, `setInvokableClass()` or another Laminas method fails | Use `setFactory()`, `setAlias()`, `get()`, `has()` or `build()` |
| The plugin managers support only the `factories` and `aliases` keys | `InvalidArgumentException` at startup | Move each `invokables` entry to `factories`. Remove `delegators`, `abstract_factories`, `initializers`, `shared`, `lazy_services` and `services` |
| The plugin managers do not create an unregistered class | `PluginNotFoundException` for a class name that is not in `factories` | Register the class in `factories` |
| The Laminas exceptions are gone | A `catch` of `ServiceNotFoundException` or `InvalidServiceException` does not catch | Catch `Psr\Container\NotFoundExceptionInterface` or `Psr\Container\ContainerExceptionInterface` |
| The plugin managers have no template parameter | A `@var GuardPluginManager<...>` annotation fails static analysis | Use the class name without a template parameter |

#### Replace an invokable guard

Before:

```php
'guard_manager' => [
    'invokables' => [
        'Ip' => IpGuard::class,
    ],
],
```

After:

```php
'guard_manager' => [
    'factories' => [
        'Ip' => static fn (): IpGuard => new IpGuard(),
    ],
],
```

Use a factory class if your configuration is cached, because a cached configuration cannot hold a closure.

#### Catch a missing guard

Before:

```php
use Laminas\ServiceManager\Exception\ServiceNotFoundException;

try {
    $guardPluginManager->get('Ip');
} catch (ServiceNotFoundException $exception) {
    // ...
}
```

After:

```php
use Psr\Container\NotFoundExceptionInterface;

try {
    $guardPluginManager->get('Ip');
} catch (NotFoundExceptionInterface $exception) {
    // ...
}
```

### Classes and types

| Change | Symptom | Fix |
| --- | --- | --- |
| `dispatchEvent()` returns `AuthorizationEvent\|ResponseInterface` | A subclass of `RbacGuardMiddleware` or `ForbiddenHandler` that overrides `dispatchEvent()` fails to load | Change the return type of your method to `AuthorizationEvent\|ResponseInterface` |
| `getRoleService()`, `getAuthorizationService()`, `getGuardFactory()` and `RbacGuardOptions::getMessagesOptions()` do not return `null` | A subclass that overrides one of them fails to load | Remove the `?` from the return type of your method |
| The properties `$roleService`, `$authorizationService` and `$guardFactory` are not nullable | A subclass that redeclares one of them fails to load | Use the new property type |
| `ForbiddenHandler` does not accept an empty template name | `InvalidArgumentException` when you create the handler | Give a template name. The factory uses `error::403` if `template_403` is empty |

### Guard behaviour

| Change | Symptom | Fix |
| --- | --- | --- |
| A `RoutePermission` rule with no permissions applies the protection policy | With `POLICY_DENY`, a route that was open returns 403 | Give the rule its permissions, or `['*']` to give access to all users |
| The controller guards use the first rule that matches, in the order of writing | A different rule applies to a route | Put the specific rules before the rules with placeholders |
| A placeholder rule matches the other characters exactly | A rule that used `.` as "any character" does not match | Write the exact characters, and use `*` for the variable part |
| `ControllerPermissionGuard` compares route names without case | A rule for a route name with capital letters applies now | Check the rules for route names with capital letters |
| An invalid rule throws `InvalidArgumentException` | An exception at startup, for a controller rule without `route` or for a rule that is not a route name | Correct the rule |
