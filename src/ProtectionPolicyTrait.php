<?php

/**
 * see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard;

use Dot\Rbac\Guard\Guard\GuardInterface;

trait ProtectionPolicyTrait
{
    /** @var string */
    protected $protectionPolicy = GuardInterface::POLICY_DENY;

    public function getProtectionPolicy(): string
    {
        return $this->protectionPolicy;
    }

    public function setProtectionPolicy(string $protectionPolicy)
    {
        $this->protectionPolicy = $protectionPolicy;
    }
}
