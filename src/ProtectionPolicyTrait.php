<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 12:55 AM
 */

namespace Dot\Rbac\Guard;

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
    public function getProtectionPolicy()
    {
        return $this->protectionPolicy;
    }

    /**
     * @param $protectionPolicy
     */
    public function setProtectionPolicy($protectionPolicy)
    {
        $this->protectionPolicy = (string)$protectionPolicy;
    }
}

