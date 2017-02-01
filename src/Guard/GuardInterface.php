<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/20/2016
 * Time: 8:39 PM
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Guard;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface GuardInterface
 * @package Dot\Rbac\Guard
 */
interface GuardInterface
{
    const POLICY_DENY = 'deny';
    const POLICY_ALLOW = 'allow';

    const CONDITION_OR = 'OR';
    const CONDITION_AND = 'AND';

    /**
     * Checks if the user is authorized to get through the guard
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function isGranted(ServerRequestInterface $request): bool;

    /**
     * @return int
     */
    public function getPriority(): int;
}
