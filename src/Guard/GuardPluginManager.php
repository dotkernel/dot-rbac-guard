<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Guard;

use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Dot\Rbac\Guard\Exception\InvalidPluginException;
use Dot\Rbac\Guard\Exception\PluginNotFoundException;
use Dot\Rbac\Guard\Factory\GuardFactory;
use Dot\Rbac\Guard\Factory\PermissionGuardFactory;
use Dot\Rbac\Guard\PluginManagerTrait;
use Psr\Container\ContainerInterface;

/**
 * A PSR-11 container for guards.
 *
 * It supports the "factories" and "aliases" keys of the "dot_authorization.guard_manager" config.
 */
class GuardPluginManager implements ContainerInterface
{
    use PluginManagerTrait;

    private const FACTORIES = [
        RouteGuard::class                => GuardFactory::class,
        RoutePermissionGuard::class      => PermissionGuardFactory::class,
        ControllerGuard::class           => GuardFactory::class,
        ControllerPermissionGuard::class => PermissionGuardFactory::class,
    ];

    private const ALIASES = [
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

    /**
     * @param array<array-key, mixed> $config
     * @throws InvalidArgumentException
     * @throws InvalidPluginException
     */
    public function __construct(ContainerInterface $container, array $config = [])
    {
        $this->initialize($container, self::FACTORIES, self::ALIASES, $config);
    }

    /**
     * Return the shared instance of a guard. Create it on the first call.
     *
     * @throws PluginNotFoundException
     * @throws InvalidPluginException
     */
    public function get(string $id): GuardInterface
    {
        $guard = $this->getPlugin($id);
        if (! $guard instanceof GuardInterface) {
            throw InvalidPluginException::fromInstance($this->getPluginLabel(), $id, GuardInterface::class, $guard);
        }

        return $guard;
    }

    /**
     * Create a new instance of a guard on each call.
     *
     * @param array<array-key, mixed>|null $options
     * @throws PluginNotFoundException
     * @throws InvalidPluginException
     */
    public function build(string $name, ?array $options = null): GuardInterface
    {
        $guard = $this->buildPlugin($name, $options);
        if (! $guard instanceof GuardInterface) {
            throw InvalidPluginException::fromInstance($this->getPluginLabel(), $name, GuardInterface::class, $guard);
        }

        return $guard;
    }

    protected function getPluginLabel(): string
    {
        return 'guard';
    }

    protected function getPluginInterface(): string
    {
        return GuardInterface::class;
    }
}
