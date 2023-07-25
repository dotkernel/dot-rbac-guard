<?php

/**
 * see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Guard\GuardInterface;

interface GuardsProviderInterface
{
    /**
     * @return GuardInterface[]
     */
    public function getGuards(): array;
}
