<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

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
     * @param $requestedName
     * @return RbacGuardOptions
     */
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        return new $requestedName($container->get('config')['dot_authorization']);
    }
}
