<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Guard;

use Dot\Rbac\Guard\ProtectionPolicyTrait;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ServerRequestInterface;

use function in_array;
use function is_array;
use function is_string;
use function lcfirst;
use function preg_match;
use function preg_quote;
use function str_contains;
use function str_replace;
use function strtolower;
use function ucwords;

abstract class AbstractGuard implements GuardInterface
{
    use ProtectionPolicyTrait;

    public const PRIORITY = 1;

    /** @var array<string, array<array-key, mixed>> */
    protected array $rules = [];

    /**
     * @param array<array-key, mixed> $options
     */
    public function __construct(array $options = [])
    {
        if (isset($options['rules']) && is_array($options['rules'])) {
            $this->setRules($options['rules']);
        }

        if (
            isset($options['protection_policy'])
            && in_array($options['protection_policy'], [self::POLICY_ALLOW, self::POLICY_DENY], true)
        ) {
            $this->setProtectionPolicy($options['protection_policy']);
        }
    }

    /**
     * @return array<string, array<array-key, mixed>>
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @param array<array-key, mixed> $rules
     */
    abstract public function setRules(array $rules): void;

    public function getPriority(): int
    {
        return static::PRIORITY;
    }

    /**
     * Check if a rule matches a route name.
     *
     * In a rule, "*" matches one or more letters, digits, "_" or "-".
     * All other characters must be the same in the rule and in the route name.
     */
    protected function routeMatches(string $rule, string $routeName): bool
    {
        if (! str_contains($rule, '*')) {
            return $rule === $routeName;
        }

        $pattern = '/^' . str_replace('\*', '[\w\-]+', preg_quote($rule, '/')) . '$/';

        return preg_match($pattern, $routeName) === 1;
    }

    /**
     * Return the first rule, in the order of writing, that matches the route name.
     */
    protected function findRule(string $routeName): ?string
    {
        foreach ($this->rules as $rule => $value) {
            if ($this->routeMatches((string) $rule, $routeName)) {
                return (string) $rule;
            }
        }

        return null;
    }

    /**
     * Return the lowercase name of the matched route, or null if no route matched.
     */
    protected function getMatchedRouteName(ServerRequestInterface $request): ?string
    {
        $routeResult = $request->getAttribute(RouteResult::class);
        if (! $routeResult instanceof RouteResult) {
            return null;
        }

        $routeName = $routeResult->getMatchedRouteName();
        if (! is_string($routeName) || $routeName === '') {
            return null;
        }

        return strtolower($routeName);
    }

    /**
     * Convert an action name, for example "change-password", to a method name, for example "changePasswordAction".
     */
    protected static function getMethodFromAction(string $action): string
    {
        $method = str_replace(['.', '-', '_'], ' ', $action);

        return lcfirst(str_replace(' ', '', ucwords($method))) . 'Action';
    }
}
