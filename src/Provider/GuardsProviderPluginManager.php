<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Factory\GuardsProviderFactory;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;

use function gettype;
use function is_object;
use function sprintf;

/**
 * @template T
 * @extends AbstractPluginManager<T>
 */
class GuardsProviderPluginManager extends AbstractPluginManager
{
    protected string $instanceOf = GuardsProviderInterface::class;

    protected array $factories = [
        ArrayGuardsProvider::class => GuardsProviderFactory::class,
    ];

    protected array $aliases = [
        'arrayguardsprovider' => ArrayGuardsProvider::class,
        'arrayGuardsProvider' => ArrayGuardsProvider::class,
        'ArrayGuardsProvider' => ArrayGuardsProvider::class,
        'arrayguards'         => ArrayGuardsProvider::class,
        'arrayGuards'         => ArrayGuardsProvider::class,
        'ArrayGuards'         => ArrayGuardsProvider::class,
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
