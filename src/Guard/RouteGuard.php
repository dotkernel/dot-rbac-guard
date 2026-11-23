<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Guard;

use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Role\RoleServiceInterface;
use Psr\Http\Message\ServerRequestInterface;

use function get_debug_type;
use function in_array;
use function is_int;
use function is_string;
use function strtolower;

class RouteGuard extends AbstractGuard
{
    public const PRIORITY = 100;

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
        foreach ($rules as $key => $value) {
            if (is_int($key)) {
                if (! is_string($value)) {
                    throw new InvalidArgumentException(
                        'A rule without a string key must be a route name, ' . get_debug_type($value) . ' given'
                    );
                }
                $this->rules[strtolower($value)] = [];
            } else {
                $this->rules[strtolower($key)] = (array) $value;
            }
        }
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
        // without a matched route (probably 404 not found), let the request go to the final handler
        $routeName = $this->getMatchedRouteName($request);
        if ($routeName === null) {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        $rule = $this->findRule($routeName);
        if ($rule === null) {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        $allowedRoles = $this->rules[$rule];
        if (in_array('*', $allowedRoles, true)) {
            return true;
        }

        return $this->roleService->matchIdentityRoles($allowedRoles);
    }
}
