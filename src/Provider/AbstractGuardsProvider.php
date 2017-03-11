<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Guard\Factory;

/**
 * Class AbstractGuardsProvider
 * @package Dot\Rbac\Guard\Provider
 */
abstract class AbstractGuardsProvider implements GuardsProviderInterface
{
    /** @var  Factory */
    protected $guardFactory;

    /**
     * AbstractGuardsProvider constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (isset($options['guard_factory']) && $options['guard_factory'] instanceof Factory) {
            $this->setGuardFactory($options['guard_factory']);
        }

        if (!$this->guardFactory instanceof Factory) {
            throw new RuntimeException('Guard factory is required and was not set');
        }
    }

    /**
     * @return Factory
     */
    public function getGuardFactory(): Factory
    {
        return $this->guardFactory;
    }

    /**
     * @param Factory $guardFactory
     */
    public function setGuardFactory(Factory $guardFactory)
    {
        $this->guardFactory = $guardFactory;
    }
}
