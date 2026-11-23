<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Fixture;

use Dot\Rbac\Guard\Guard\AbstractGuard;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Makes the protected helpers of AbstractGuard public for the tests.
 */
final class ExposedGuard extends AbstractGuard
{
    /**
     * @param array<array-key, mixed> $rules
     */
    public function setRules(array $rules): void
    {
    }

    public function isGranted(ServerRequestInterface $request): bool
    {
        return false;
    }

    public function matches(string $rule, string $routeName): bool
    {
        return $this->routeMatches($rule, $routeName);
    }

    public static function methodFromAction(string $action): string
    {
        return self::getMethodFromAction($action);
    }
}
