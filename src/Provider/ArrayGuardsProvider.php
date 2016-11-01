<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 2:49 AM
 */

namespace Dot\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\GuardInterface;
use Dot\Rbac\Guard\GuardPluginManager;

/**
 * Class GuardsProvider
 * @package Dot\Rbac\Guard\Provider
 */
class ArrayGuardsProvider extends AbstractGuardsProvider
{
    /** @var array */
    protected $guardsConfig = [];

    /** @var GuardInterface[] */
    protected $guards;

    /**
     * ArrayGuardsProvider constructor.
     * @param GuardPluginManager $manager
     * @param array $guardsConfig
     */
    public function __construct(GuardPluginManager $manager, array $guardsConfig = null)
    {
        $this->guardsConfig = $guardsConfig;
        parent::__construct($manager);

    }

    /**
     * Gets the  cached guard list or creates it from the config
     * @return GuardInterface[]
     */
    public function getGuards()
    {
        if ($this->guards) {
            return $this->guards;
        }

        if (empty($this->guardsConfig)) {
            return [];
        }

        $this->guards = [];
        foreach ($this->guardsConfig as $name => $config) {
            if ($this->guardManager->has($name)) {
                $this->guards[] = $this->guardManager->get($name, $config);
            } else {
                throw new RuntimeException(sprintf("Guard %s is not registered in the guard plugin manager", $name));
            }
        }

        $this->sortGuardsByPriority();
        return $this->guards;
    }

    /**
     * Sort the guards list internally
     *
     * @return void
     */
    protected function sortGuardsByPriority()
    {
        usort($this->guards, function (GuardInterface $a, GuardInterface $b) {
            return $b->getPriority() - $a->getPriority();
        });
    }
}