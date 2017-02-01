<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 2:49 AM
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Guard\GuardInterface;

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
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        if (isset($options['guards']) && is_array($options['guards'])) {
            $this->setGuardsConfig($options['guards']);
        }
    }

    /**
     * Gets the  cached guard list or creates it from the config
     * @return GuardInterface[]
     */
    public function getGuards() : array
    {
        if ($this->guards) {
            return $this->guards;
        }

        if (empty($this->guardsConfig)) {
            return [];
        }

        $this->guards = [];
        foreach ($this->guardsConfig as $guardConfig) {
            $this->guards[] = $this->getGuardFactory()->create($guardConfig);
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

    /**
     * @return array
     */
    public function getGuardsConfig(): array
    {
        return $this->guardsConfig;
    }

    /**
     * @param array $guardsConfig
     */
    public function setGuardsConfig(array $guardsConfig)
    {
        $this->guardsConfig = $guardsConfig;
    }
}
