# Changelog

All notable changes to this package are documented in this file.

## 5.0.0 - TBD

### Changed

* Requires PHP 8.3, 8.4, 8.5 or 8.6.
* `GuardPluginManager` and `GuardsProviderPluginManager` are PSR-11 containers and no longer extend `Laminas\ServiceManager\AbstractPluginManager`. They support only the `factories` and `aliases` keys.
* `dispatchEvent()` returns `AuthorizationEvent|ResponseInterface`.
* `getRoleService()`, `getAuthorizationService()`, `getGuardFactory()` and `RbacGuardOptions::getMessagesOptions()` do not return `null`.
* A permission rule with no permissions applies the protection policy in `RoutePermissionGuard`, as in `ControllerPermissionGuard`.
* The controller guards use the first rule that matches, in the order of writing, as the route guards do.
* In a placeholder rule, all characters other than `*` must match exactly.
* An invalid guard rule throws `InvalidArgumentException`.
* `ForbiddenHandler` does not accept an empty template name.
* Allows `mezzio/mezzio-router` 4.x and `mezzio/mezzio-template` 3.x.
* PHPStan level 8 and PHPUnit 12. PHPUnit fails on notices, warnings, deprecations and risky tests.
* The tests use fixture classes instead of mocks.
* GitHub Actions: `actions/checkout@v7`, `actions/cache@v6` and `codecov/codecov-action@v7`.

### Added

* `PluginNotFoundException` and `InvalidPluginException`, which implement the PSR-11 exception interfaces.
* `setFactory()` and `setAlias()` on the plugin managers.
* Direct requirements for the packages that the code uses: `dotkernel/dot-authorization`, `dotkernel/dot-event`, `laminas/laminas-eventmanager`, `psr/container` and `psr/http-server-handler`.
* Version 5 documentation, with an events page and an upgrade guide.

### Deprecated

* Nothing

### Removed

* Support for PHP 8.2.
* The requirements `laminas/laminas-servicemanager` and `dotkernel/dot-controller`.
* Support for plugin manager configuration keys other than `factories` and `aliases`.

### Fixed

* A listener that returned a response caused a `TypeError`.
* `ControllerPermissionGuard` ignored the rules for route names with capital letters.
* A placeholder rule with `/` or other regex characters caused a PHP warning, and the guard ignored the rule.
* Missing configuration keys caused PHP warnings and a `TypeError`.
* `authorization-guards.global.php.dist` and the documentation examples had PHP errors and a wrong class name.

## 4.2.1 - 2025-11-10

### Changed

* Version 4 documentation.

### Added

* Route name placeholders.

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* PHPUnit deprecations.

## 4.2.0 - 2025-11-03

### Changed

* PHP 8.4 and 8.5 support (Issue #47).

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* PHP 8.1 support (Issue #47).

### Fixed

* Nothing

## 4.1.0 - 2025-03-14

### Changed

* PHPStan replaces Psalm (Issue #39).

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 4.0.1 - 2025-01-28

### Changed

* Psalm 6.x (Issue #35).

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 4.0.0 - 2025-01-23

### Changed

* Support for `laminas/laminas-servicemanager` 4.x (Issue #33).

### Added

* Tests for `GuardPluginManager` and `GuardsProviderPluginManager`.

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.7.0 - 2025-11-03

### Changed

* PHP 8.4 and 8.5 support (Issue #46).

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* PHP 8.1 support (Issue #46).

### Fixed

* Nothing

## 3.6.1 - 2025-10-21

### Changed

* Tests and documentation.

### Added

* Route name placeholders.

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.6.0 - 2025-03-14

### Changed

* PHPStan replaces Psalm (Issue #38).

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.5.0 - 2024-05-03

### Changed

* Code review.
* `composer.json`, documentation, workflows and `SECURITY.md`.

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.4.3 - 2024-01-23

### Changed

* Dependency updates.

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.4.2 - 2023-12-07

### Changed

* PHP 8.3 support.

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.4.1 - 2023-08-25

### Changed

* Nothing

### Added

* Codecov.

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.4.0 - 2023-07-27

### Changed

* Nothing

### Added

* Unit tests and `phpunit.xml`.

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* An anonymous class type hint.

## 3.3.0 - 2023-03-29

### Changed

* Nothing

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* `laminas/laminas-dependency-plugin`.

### Fixed

* Nothing

## 3.2.1 - 2022-05-26

### Changed

* Nothing

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Updated validation for routeResult

## 3.2.0 - 2022-04-29

### Changed

* Added PHP 8.0 and 8.1 compatibility.

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.1.0 - 2020-11-03

### Changed

* Composer 2 compatibility.
* Update composer.json to require version 2 of laminas/laminas-dependency-plugin to be Composer 2 compatible.

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* bump min version of laminas/laminas-dependency-plugin
* bump min version of dotkernel/dot-rbac

## 1.0.0 - 2018-05-16

ZE3 migration

### Changed

* updated middleware implementation

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 0.2.0 - 2017-03-22

ZE2 migration

### Changed

* updated to PSR11 container
* updated middleware to implement PSR15 `MiddlewareInterface`
* updated forbidden error handler for ZE2

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 0.1.1 - 2017-03-11

Updated php file headers doc blocks

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 0.1.0 - 2017-03-09

Initial tagged release

### Added

* Everything

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing
