<?php

/**
 * see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Guard\Factory;

abstract class AbstractGuardsProvider implements GuardsProviderInterface
{
    /** @var  Factory */
    protected $guardFactory;

    public function __construct(array $options = [])
    {
        if (isset($options['guard_factory']) && $options['guard_factory'] instanceof Factory) {
            $this->setGuardFactory($options['guard_factory']);
        }

        if (! $this->guardFactory instanceof Factory) {
            throw new RuntimeException('Guard factory is required and was not set');
        }
    }

    public function getGuardFactory(): Factory
    {
        return $this->guardFactory;
    }

    public function setGuardFactory(Factory $guardFactory)
    {
        $this->guardFactory = $guardFactory;
    }
}
