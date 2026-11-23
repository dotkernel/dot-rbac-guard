<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Guard\GuardInterface;

interface GuardsProviderInterface
{
    /**
     * @return list<GuardInterface>
     */
    public function getGuards(): array;
}
