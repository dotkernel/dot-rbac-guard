<?php

/**
 * see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Guard;

use Psr\Http\Message\ServerRequestInterface;

interface GuardInterface
{
    public const POLICY_DENY  = 'deny';
    public const POLICY_ALLOW = 'allow';

    public const CONDITION_OR  = 'OR';
    public const CONDITION_AND = 'AND';

    /**
     * Checks if the user is authorized to get through the guard
     */
    public function isGranted(ServerRequestInterface $request): bool;

    public function getPriority(): int;
}
