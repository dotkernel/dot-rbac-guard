<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Guard;

use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Exception\InvalidArgumentException;

use function get_debug_type;
use function in_array;
use function is_array;
use function is_string;
use function sprintf;

/**
 * The permission check of RoutePermissionGuard and ControllerPermissionGuard.
 */
trait PermissionGuardTrait
{
    protected AuthorizationInterface $authorizationService;

    public function getAuthorizationService(): AuthorizationInterface
    {
        return $this->authorizationService;
    }

    public function setAuthorizationService(AuthorizationInterface $authorizationService): void
    {
        $this->authorizationService = $authorizationService;
    }

    /**
     * Check a permission rule.
     *
     * The rule is a list of permissions, or an array with the keys "permissions" and "condition".
     * A rule with no permissions applies the protection policy.
     *
     * @param array<array-key, mixed> $rule
     * @throws InvalidArgumentException
     */
    protected function isGrantedByPermissions(array $rule): bool
    {
        $permissions = $rule['permissions'] ?? $rule;
        $condition   = $rule['condition'] ?? GuardInterface::CONDITION_AND;

        if (! is_array($permissions) || $permissions === []) {
            return $this->protectionPolicy === GuardInterface::POLICY_ALLOW;
        }

        if (in_array('*', $permissions, true)) {
            return true;
        }

        if ($condition === GuardInterface::CONDITION_AND) {
            foreach ($permissions as $permission) {
                if (! is_string($permission) || ! $this->authorizationService->isGranted($permission)) {
                    return false;
                }
            }

            return true;
        }

        if ($condition === GuardInterface::CONDITION_OR) {
            foreach ($permissions as $permission) {
                if (is_string($permission) && $this->authorizationService->isGranted($permission)) {
                    return true;
                }
            }

            return false;
        }

        throw new InvalidArgumentException(sprintf(
            'Condition must be either "AND" or "OR", %s given',
            get_debug_type($condition)
        ));
    }
}
