<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Guard\Factory;

abstract class AbstractGuardsProvider implements GuardsProviderInterface
{
    protected ?Factory $guardFactory = null;

    public function __construct(array $options = [])
    {
        if (isset($options['guard_factory']) && $options['guard_factory'] instanceof Factory) {
            $this->setGuardFactory($options['guard_factory']);
        }

        if (! $this->guardFactory instanceof Factory) {
            throw new RuntimeException('Guard factory is required and was not set');
        }
    }

    public function getGuardFactory(): ?Factory
    {
        return $this->guardFactory;
    }

    public function setGuardFactory(Factory $guardFactory): void
    {
        $this->guardFactory = $guardFactory;
    }
}
