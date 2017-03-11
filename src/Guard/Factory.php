<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

namespace Dot\Rbac\Guard\Guard;

use Dot\Rbac\Guard\Exception\RuntimeException;
use Interop\Container\ContainerInterface;

/**
 * Class Factory
 * @package Dot\Rbac\Guard\Guard
 */
class Factory
{
    /** @var  ContainerInterface */
    protected $container;

    /** @var  GuardPluginManager */
    protected $guardPluginManager;

    /**
     * Factory constructor.
     * @param ContainerInterface $container
     * @param GuardPluginManager|null $guardPluginManager
     */
    public function __construct(ContainerInterface $container, GuardPluginManager $guardPluginManager = null)
    {
        $this->container = $container;
        $this->guardPluginManager = $guardPluginManager;
    }

    /**
     * @param array $specs
     * @return GuardInterface
     */
    public function create(array $specs): GuardInterface
    {
        $type = $specs['type'] ?? '';
        if (empty($type)) {
            throw new RuntimeException('Guard type was not provided');
        }

        $guardPluginManager = $this->getGuardPluginManager();
        return $guardPluginManager->get($type, $specs['options'] ?? null);
    }

    /**
     * @return GuardPluginManager
     */
    public function getGuardPluginManager(): GuardPluginManager
    {
        if (!$this->guardPluginManager) {
            $this->guardPluginManager = new GuardPluginManager($this->container, []);
        }

        return $this->guardPluginManager;
    }
}
