# dot-rbac-guard

Defines authorization guards that authorize users to access parts of an application, based on routes, controller actions, roles and permissions.
The authorization service of `dotkernel/dot-rbac` checks authorization at a narrow level.
The guards work as gateways to larger parts of an application.
For better security, use both in an application.
The package works with any PSR-11 container.

## Documentation

Documentation is available at: https://docs.dotkernel.org/dot-rbac-guard/.

## Versions

| Version | Supported | PHP versions |
| --- | --- | --- |
| 5.x | Yes | 8.3, 8.4, 8.5, 8.6 |
| 4.x | Yes | 8.2, 8.3, 8.4, 8.5 |
| 3.x | No | 8.2, 8.3, 8.4, 8.5 |

To upgrade from 4.x, read the "Upgrading" page of the version 5 documentation.

## Badges

![OSS Lifecycle](https://img.shields.io/osslifecycle/dotkernel/dot-rbac-guard)
![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-rbac-guard/5.0.0)

[![GitHub issues](https://img.shields.io/github/issues/dotkernel/dot-rbac-guard)](https://github.com/dotkernel/dot-rbac-guard/issues)
[![GitHub forks](https://img.shields.io/github/forks/dotkernel/dot-rbac-guard)](https://github.com/dotkernel/dot-rbac-guard/network)
[![GitHub stars](https://img.shields.io/github/stars/dotkernel/dot-rbac-guard)](https://github.com/dotkernel/dot-rbac-guard/stargazers)
[![GitHub license](https://img.shields.io/github/license/dotkernel/dot-rbac-guard)](https://github.com/dotkernel/dot-rbac-guard/blob/5.0/LICENSE.md)

[![Build Static](https://github.com/dotkernel/dot-rbac-guard/actions/workflows/continuous-integration.yml/badge.svg?branch=5.0)](https://github.com/dotkernel/dot-rbac-guard/actions/workflows/continuous-integration.yml)
[![codecov](https://codecov.io/gh/dotkernel/dot-rbac-guard/graph/badge.svg?token=HB718GSIKS)](https://codecov.io/gh/dotkernel/dot-rbac-guard)
[![PHPStan](https://github.com/dotkernel/dot-rbac-guard/actions/workflows/static-analysis.yml/badge.svg?branch=5.0)](https://github.com/dotkernel/dot-rbac-guard/actions/workflows/static-analysis.yml)

## Installation

Run this command in the root directory of your project:

```shell
composer require dotkernel/dot-rbac-guard
```

The guards use the authorization service of `dotkernel/dot-rbac`.
Composer installs that package too.
Configure `dot-rbac` before you use the guards.

## Usage

1. Add `Dot\Rbac\Guard\ConfigProvider` to your application configuration.
2. Configure the guards in `config/autoload/authorization-guards.global.php`. The file `authorization-guards.global.php.dist` of this package is an example.
3. Add `ForbiddenHandler` and `RbacGuardMiddleware` to the pipeline, between `RouteMiddleware` and `DispatchMiddleware`.

The documentation explains each step, the guards, the events and the configuration keys.
