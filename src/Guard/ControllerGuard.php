<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Guard;

use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Role\RoleServiceInterface;
use Psr\Http\Message\ServerRequestInterface;

use function in_array;
use function is_array;

class ControllerGuard extends AbstractGuard
{
    use ControllerRulesTrait;

    public const PRIORITY = 40;

    protected RoleServiceInterface $roleService;

    /**
     * @param array<array-key, mixed>|null $options
     */
    public function __construct(?array $options = null)
    {
        $options = $options ?? [];
        parent::__construct($options);

        if (isset($options['role_service']) && $options['role_service'] instanceof RoleServiceInterface) {
            $this->setRoleService($options['role_service']);
        }

        if (! isset($this->roleService)) {
            throw new RuntimeException('RoleService is required by this guard and was not set');
        }
    }

    /**
     * @param array<array-key, mixed> $rules
     * @throws InvalidArgumentException
     */
    public function setRules(array $rules): void
    {
        $this->addControllerRules($rules, 'roles');
    }

    public function getRoleService(): RoleServiceInterface
    {
        return $this->roleService;
    }

    public function setRoleService(RoleServiceInterface $roleService): void
    {
        $this->roleService = $roleService;
    }

    public function isGranted(ServerRequestInterface $request): bool
    {
        $allowedRoles = $this->findControllerRule($request);
        if (! is_array($allowedRoles)) {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        if (in_array('*', $allowedRoles, true)) {
            return true;
        }

        return $this->roleService->matchIdentityRoles($allowedRoles);
    }
}
