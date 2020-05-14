<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Factory\GuardsProviderFactory;
use Laminas\ServiceManager\AbstractPluginManager;

/**
 * Class GuardsProviderPluginManager
 * @package Dot\Rbac\Guard\Provider
 */
class GuardsProviderPluginManager extends AbstractPluginManager
{
    protected $instanceOf = GuardsProviderInterface::class;

    protected $factories = [
        ArrayGuardsProvider::class => GuardsProviderFactory::class,
    ];

    protected $aliases = [
        'arrayguardsprovider' => ArrayGuardsProvider::class,
        'arrayGuardsProvider' => ArrayGuardsProvider::class,
        'ArrayGuardsProvider' => ArrayGuardsProvider::class,
        'arrayguards' => ArrayGuardsProvider::class,
        'arrayGuards' => ArrayGuardsProvider::class,
        'ArrayGuards' => ArrayGuardsProvider::class,
    ];
}
