<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Fixture;

use Dot\Authorization\Role\RoleInterface;
use Dot\Rbac\Role\Role;
use Dot\Rbac\Role\RoleServiceInterface;

use function array_intersect;
use function array_map;
use function array_values;

/**
 * Matches the roles of the identity it was given.
 */
final class RoleService implements RoleServiceInterface
{
    /**
     * @param list<string> $identityRoles
     */
    public function __construct(private readonly array $identityRoles = [])
    {
    }

    public function getIdentity(): mixed
    {
        return null;
    }

    public function getGuestRole(): string
    {
        return 'guest';
    }

    /**
     * @return list<RoleInterface>
     */
    public function getIdentityRoles(): array
    {
        return $this->resolveRoles($this->identityRoles);
    }

    /**
     * @param array<array-key, mixed> $roles
     */
    public function matchIdentityRoles(array $roles): bool
    {
        $roleNames = array_map(static fn (mixed $role): string => (string) $role, $roles);

        return array_intersect($roleNames, $this->identityRoles) !== [];
    }

    /**
     * Required by RoleServiceInterface in dot-rbac 5. Not part of the interface in dot-rbac 4.
     *
     * @param array<array-key, mixed> $roles
     * @return list<RoleInterface>
     */
    public function resolveRoles(array $roles): array
    {
        return array_map(
            static fn (mixed $role): RoleInterface => $role instanceof RoleInterface ? $role : new Role((string) $role),
            array_values($roles)
        );
    }
}
