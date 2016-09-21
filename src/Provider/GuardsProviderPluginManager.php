<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 12:45 AM
 */

namespace Dot\Rbac\Guard\Provider;


use Dot\Rbac\Guard\Factory\ArrayGuardsProviderFactory;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Class GuardsProviderPluginManager
 * @package Dot\Rbac\Guard\Provider
 */
class GuardsProviderPluginManager extends AbstractPluginManager
{
    protected $instanceOf = GuardsProviderInterface::class;

    protected $factories = [
        ArrayGuardsProvider::class => ArrayGuardsProviderFactory::class,
    ];
}