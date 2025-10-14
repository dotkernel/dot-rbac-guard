<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Guard;

use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Role\RoleServiceInterface;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ServerRequestInterface;

use function array_keys;
use function in_array;
use function is_int;
use function preg_match;
use function sprintf;
use function str_contains;
use function str_replace;
use function strtolower;

class RouteGuard extends AbstractGuard
{
    public const PRIORITY = 100;

    protected ?RoleServiceInterface $roleService = null;

    protected array $rules = [];

    public function __construct(?array $options = null)
    {
        $options = $options ?? [];
        parent::__construct($options);

        if (isset($options['role_service']) && $options['role_service'] instanceof RoleServiceInterface) {
            $this->setRoleService($options['role_service']);
        }

        if (! $this->roleService instanceof RoleServiceInterface) {
            throw new RuntimeException('RoleService is required by this guard and was not set');
        }
    }

    public function setRules(array $rules): void
    {
        foreach ($rules as $key => $value) {
            if (is_int($key)) {
                $routeName = strtolower($value);
                $roles     = [];
            } else {
                $routeName = strtolower($key);
                $roles     = (array) $value;
            }

            $this->rules[$routeName] = $roles;
        }
    }

    public function getRoleService(): ?RoleServiceInterface
    {
        return $this->roleService;
    }

    public function setRoleService(RoleServiceInterface $roleService): void
    {
        $this->roleService = $roleService;
    }

    public function isGranted(ServerRequestInterface $request): bool
    {
        $routeResult = $request->getAttribute(RouteResult::class, false);
        //if we do not have a matched route(probably 404 not found) let it go to the final handler
        if (! $routeResult instanceof RouteResult || ! $routeResult->getMatchedRouteName()) {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        $matchedRouteName = strtolower($routeResult->getMatchedRouteName());
        $allowedRoles     = null;

        foreach (array_keys($this->rules) as $routeName) {
            if (str_contains($routeName, '*')) {
                $routePattern = sprintf(
                    "/^%s$/",
                    str_replace('*', '[\w\-]+', $routeName)
                );

                $routeMatched = preg_match($routePattern, $matchedRouteName);

                if ($routeMatched === 1) {
                    $allowedRoles = $this->rules[$routeName];
                    break;
                }
            } else {
                if ($routeName === $matchedRouteName) {
                    $allowedRoles = $this->rules[$routeName];
                    break;
                }
            }
        }

        if (null === $allowedRoles) {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        if (in_array('*', $allowedRoles)) {
            return true;
        }

        return $this->roleService->matchIdentityRoles($allowedRoles);
    }
}
