<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
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
