<?php

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
