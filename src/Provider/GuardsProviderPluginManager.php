<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Factory\GuardsProviderFactory;
use Laminas\ServiceManager\AbstractPluginManager;

/**
 * @template T
 * @extends AbstractPluginManager<T>
 */
class GuardsProviderPluginManager extends AbstractPluginManager
{
    /** @var string */
    protected $instanceOf = GuardsProviderInterface::class;

    /** @var string[] */
    protected $factories = [
        ArrayGuardsProvider::class => GuardsProviderFactory::class,
    ];

    /** @var string[] */
    protected $aliases = [
        'arrayguardsprovider' => ArrayGuardsProvider::class,
        'arrayGuardsProvider' => ArrayGuardsProvider::class,
        'ArrayGuardsProvider' => ArrayGuardsProvider::class,
        'arrayguards'         => ArrayGuardsProvider::class,
        'arrayGuards'         => ArrayGuardsProvider::class,
        'ArrayGuards'         => ArrayGuardsProvider::class,
    ];
}
