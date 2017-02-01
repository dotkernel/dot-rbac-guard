<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 12:55 AM
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
