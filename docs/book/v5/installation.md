# Installation

Version 5 requires PHP 8.3, 8.4, 8.5 or 8.6.

Run this command in the root directory of your project:

```shell
composer require dotkernel/dot-rbac-guard
```

The guards use the authorization service of `dotkernel/dot-rbac`.
Composer installs that package too.
Configure `dot-rbac` before you use the guards.

Add the `ConfigProvider` of the package to your application configuration.
It registers the factories of the services and the default configuration:

```php
$aggregator = new ConfigAggregator([
    // ...
    \Dot\Rbac\ConfigProvider::class,
    \Dot\Rbac\Guard\ConfigProvider::class,
    // ...
]);
```

The package works with any PSR-11 container that accepts a factory for each service name.
If your application uses Laminas Service Manager, require `laminas/laminas-servicemanager` in your application.
