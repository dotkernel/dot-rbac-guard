<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Guard;

use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ServerRequestInterface;

use function is_array;
use function is_string;
use function sprintf;
use function strtolower;

/**
 * The rule storage and lookup of ControllerGuard and ControllerPermissionGuard.
 *
 * The rules are stored by route name, then by action method name. The key 0 holds the rule for all actions.
 */
trait ControllerRulesTrait
{
    /**
     * @param array<array-key, mixed> $rules
     * @param 'roles'|'permissions' $valueKey
     * @throws InvalidArgumentException
     */
    private function addControllerRules(array $rules, string $valueKey): void
    {
        foreach ($rules as $rule) {
            if (! is_array($rule) || ! isset($rule['route']) || ! is_string($rule['route'])) {
                throw new InvalidArgumentException(
                    'Each controller guard rule must have a "route" key with a string value'
                );
            }

            $route   = strtolower($rule['route']);
            $actions = isset($rule['actions']) ? (array) $rule['actions'] : [];
            $values  = (array) ($rule[$valueKey] ?? []);

            if ($actions === []) {
                $this->rules[$route][0] = $values;
                continue;
            }

            foreach ($actions as $action) {
                if (! is_string($action)) {
                    throw new InvalidArgumentException(sprintf(
                        'The actions of the controller guard rule for route "%s" must be strings',
                        $route
                    ));
                }
                $this->rules[$route][static::getMethodFromAction($action)] = $values;
            }
        }
    }

    /**
     * Return the roles or permissions that apply to the request, or null if no rule applies.
     *
     * @return array<array-key, mixed>|null
     */
    private function findControllerRule(ServerRequestInterface $request): ?array
    {
        $routeName = $this->getMatchedRouteName($request);
        if ($routeName === null) {
            return null;
        }

        $rule = $this->findRule($routeName);
        if ($rule === null) {
            return null;
        }

        $routeResult = $request->getAttribute(RouteResult::class);
        $params      = $routeResult instanceof RouteResult ? $routeResult->getMatchedParams() : [];
        $action      = isset($params['action']) && is_string($params['action']) && $params['action'] !== ''
            ? $params['action']
            : 'index';
        $method      = static::getMethodFromAction($action);

        $value = $this->rules[$rule][$method] ?? $this->rules[$rule][0] ?? null;

        return is_array($value) ? $value : null;
    }
}
