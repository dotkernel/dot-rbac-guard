<?php
/**
 * @copyright: DotKernel
 * @library: dot-rbac-guard
 * @author: n3vrax
 * Date: 2/1/2017
 * Time: 3:43 AM
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Guard;

use Dot\Rbac\Guard\ProtectionPolicyTrait;

/**
 * Class AbstractGuard
 * @package Dot\Rbac\Guard\Guard
 */
abstract class AbstractGuard implements GuardInterface
{
    use ProtectionPolicyTrait;

    const PRIORITY = 1;

    /** @var array */
    protected $rules = [];

    /**
     * AbstractGuard constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (isset($options['rules']) && is_array($options['rules'])) {
            $this->setRules($options['rules']);
        }

        if (isset($options['protection_policy'])
            && in_array($options['protection_policy'], [GuardInterface::POLICY_ALLOW, GuardInterface::POLICY_DENY])) {
            $this->setProtectionPolicy($options['protection_policy']);
        }
    }

    /**
     * @param array $rules
     */
    abstract public function setRules(array $rules);

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @return int
     */
    public function getPriority() : int
    {
        return static::PRIORITY;
    }
}
