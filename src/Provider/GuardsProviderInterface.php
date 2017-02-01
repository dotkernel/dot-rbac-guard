<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 9/20/2016
 * Time: 10:42 PM
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Guard\GuardInterface;

/**
 * Interface GuardsProviderInterface
 * @package Dot\Rbac\Guard\Provider
 */
interface GuardsProviderInterface
{
    /**
     * @return GuardInterface[]
     */
    public function getGuards() : array;
}
