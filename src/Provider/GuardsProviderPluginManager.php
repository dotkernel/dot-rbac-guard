<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 12:45 AM
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Factory\GuardsProviderFactory;
use Zend\ServiceManager\AbstractPluginManager;

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
