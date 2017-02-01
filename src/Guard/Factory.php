<?php
/**
 * @copyright: DotKernel
 * @library: dot-rbac-guard
 * @author: n3vrax
 * Date: 2/1/2017
 * Time: 4:02 AM
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
        return $guardPluginManager->get($type, $specs['options'] ?? []);
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
