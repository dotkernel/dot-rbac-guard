<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Guard;

use Dot\Rbac\Guard\Factory\GuardFactory;
use Dot\Rbac\Guard\Factory\PermissionGuardFactory;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;

use function gettype;
use function is_object;
use function sprintf;

/**
 * @template T
 * @extends AbstractPluginManager<T>
 */
class GuardPluginManager extends AbstractPluginManager
{
    protected string $instanceOf = GuardInterface::class;

    protected array $factories = [
        RouteGuard::class                => GuardFactory::class,
        RoutePermissionGuard::class      => PermissionGuardFactory::class,
        ControllerGuard::class           => GuardFactory::class,
        ControllerPermissionGuard::class => PermissionGuardFactory::class,
    ];

    protected array $aliases = [
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

    public function validate(mixed $instance): void
    {
        if (! $instance instanceof $this->instanceOf) {
            throw new InvalidServiceException(sprintf(
                '%s can only create instances of %s; %s is invalid',
                static::class,
                $this->instanceOf,
                is_object($instance) ? $instance::class : gettype($instance)
            ));
        }
    }
}
