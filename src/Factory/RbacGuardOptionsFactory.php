<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 12:50 AM
 */

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Interop\Container\ContainerInterface;

/**
 * Class RbacGuardOptionsFactory
 * @package Dot\Rbac\Guard\Factory
 */
class RbacGuardOptionsFactory
{
    /**
     * @param ContainerInterface $container
     * @return RbacGuardOptions
     */
    public function __invoke(ContainerInterface $container)
    {
        return new RbacGuardOptions($container->get('config')['dot_authorization']);
    }
}
