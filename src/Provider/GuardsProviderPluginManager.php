<?php

/**
 * see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

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
