<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 5/21/2016
 * Time: 12:50 AM
 */

namespace Dot\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Interop\Container\ContainerInterface;

class ModuleOptionsFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new RbacGuardOptions($container->get('config')['dk_authorization']);
    }
}