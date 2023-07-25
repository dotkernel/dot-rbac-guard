<?php

/**
 * see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Guard;

use Dot\Rbac\Guard\Factory\GuardFactory;
use Dot\Rbac\Guard\Factory\PermissionGuardFactory;
use Laminas\ServiceManager\AbstractPluginManager;

/**
 * @template T
 * @extends AbstractPluginManager<T>
 */
class GuardPluginManager extends AbstractPluginManager
{
    /** @var string */
    protected $instanceOf = GuardInterface::class;

    /** @var string[] */
    protected $factories = [
        RouteGuard::class                => GuardFactory::class,
        RoutePermissionGuard::class      => PermissionGuardFactory::class,
        ControllerGuard::class           => GuardFactory::class,
        ControllerPermissionGuard::class => PermissionGuardFactory::class,
    ];

    /** @var string[] */
    protected $aliases = [
        'routeguard'                => RouteGuard::class,
        'routeGuard'                => RouteGuard::class,
        'RouteGuard'                => RouteGuard::class,
        'route'                     => RouteGuard::class,
        'Route'                     => RouteGuard::class,
        'routepermissionguard'      => RoutePermissionGuard::class,
        'routePermissionGuard'      => RoutePermissionGuard::class,
        'RoutePermissionGuard'      => RoutePermissionGuard::class,
        'routepermission'           => RoutePermissionGuard::class,
        'routePermission'           => RoutePermissionGuard::class,
        'RoutePermission'           => RoutePermissionGuard::class,
        'controllerguard'           => ControllerGuard::class,
        'controllerGuard'           => ControllerGuard::class,
        'ControllerGuard'           => ControllerGuard::class,
        'controller'                => ControllerGuard::class,
        'Controller'                => ControllerGuard::class,
        'controllerpermissionguard' => ControllerPermissionGuard::class,
        'controllerPermissionGuard' => ControllerPermissionGuard::class,
        'ControllerPermissionGuard' => ControllerPermissionGuard::class,
        'controllerpermission'      => ControllerPermissionGuard::class,
        'controllerPermission'      => ControllerPermissionGuard::class,
        'ControllerPermission'      => ControllerPermissionGuard::class,
    ];
}
