<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Guard;

use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Psr\Http\Message\ServerRequestInterface;

use function get_debug_type;
use function is_int;
use function is_string;
use function strtolower;

class RoutePermissionGuard extends AbstractGuard
{
    use PermissionGuardTrait;

    public const PRIORITY = 70;

    /**
     * @param array<array-key, mixed>|null $options
     */
    public function __construct(?array $options = null)
    {
        $options = $options ?? [];
        parent::__construct($options);

        if (
            isset($options['authorization_service'])
            && $options['authorization_service'] instanceof AuthorizationInterface
        ) {
            $this->setAuthorizationService($options['authorization_service']);
        }

        if (! isset($this->authorizationService)) {
            throw new RuntimeException('Authorization service is required by this guard and was not set');
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

    /**
     * @throws InvalidArgumentException
     */
    public function isGranted(ServerRequestInterface $request): bool
    {
        // without a matched route (probably 404 not found), let the request go to the final handler
        $routeName = $this->getMatchedRouteName($request);
        if ($routeName === null) {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        // if no rule applies, the protection policy decides
        $rule = $this->findRule($routeName);
        if ($rule === null) {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        return $this->isGrantedByPermissions($this->rules[$rule]);
    }
}
