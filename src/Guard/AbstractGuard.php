<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Guard;

use Dot\Rbac\Guard\ProtectionPolicyTrait;

use function in_array;
use function is_array;

abstract class AbstractGuard implements GuardInterface
{
    use ProtectionPolicyTrait;

    public const PRIORITY = 1;

    protected array $rules = [];

    public function __construct(array $options = [])
    {
        if (isset($options['rules']) && is_array($options['rules'])) {
            $this->setRules($options['rules']);
        }

        if (
            isset($options['protection_policy'])
            && in_array($options['protection_policy'], [GuardInterface::POLICY_ALLOW, GuardInterface::POLICY_DENY])
        ) {
            $this->setProtectionPolicy($options['protection_policy']);
        }
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    abstract public function setRules(array $rules): void;

    public function getPriority(): int
    {
        return static::PRIORITY;
    }
}
