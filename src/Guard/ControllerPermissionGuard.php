<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Guard;

use Dot\Authorization\AuthorizationInterface;
use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Psr\Http\Message\ServerRequestInterface;

use function is_array;

class ControllerPermissionGuard extends AbstractGuard
{
    use ControllerRulesTrait;
    use PermissionGuardTrait;

    public const PRIORITY = 10;

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
        $this->addControllerRules($rules, 'permissions');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function isGranted(ServerRequestInterface $request): bool
    {
        $allowedPermissions = $this->findControllerRule($request);
        if (! is_array($allowedPermissions)) {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        return $this->isGrantedByPermissions($allowedPermissions);
    }
}
