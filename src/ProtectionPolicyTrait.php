<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard;

use Dot\Rbac\Guard\Guard\GuardInterface;

/**
 * Class ProtectionPolicyTrait
 * @package Dot\Rbac\Guard
 */
trait ProtectionPolicyTrait
{
    /** @var string */
    protected $protectionPolicy = GuardInterface::POLICY_DENY;

    /**
     * @return string
     */
    public function getProtectionPolicy(): string
    {
        return $this->protectionPolicy;
    }

    /**
     * @param $protectionPolicy
     */
    public function setProtectionPolicy(string $protectionPolicy)
    {
        $this->protectionPolicy = $protectionPolicy;
    }
}
