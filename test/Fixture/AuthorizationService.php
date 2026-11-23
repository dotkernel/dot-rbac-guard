<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Fixture;

use Dot\Authorization\AuthorizationInterface;

use function in_array;

/**
 * Grants the permissions it was given.
 */
final class AuthorizationService implements AuthorizationInterface
{
    /** @var list<string> */
    public array $checked = [];

    /**
     * @param list<string> $grantedPermissions
     */
    public function __construct(private readonly array $grantedPermissions = [])
    {
    }

    /**
     * @param array<array-key, mixed> $roles
     */
    public function isGranted(string $permission, array $roles = [], mixed $context = null): bool
    {
        $this->checked[] = $permission;

        return in_array($permission, $this->grantedPermissions, true);
    }
}
