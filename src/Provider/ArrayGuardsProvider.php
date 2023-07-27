<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Guard\GuardInterface;

use function is_array;
use function usort;

class ArrayGuardsProvider extends AbstractGuardsProvider
{
    protected array $guardsConfig = [];

    protected array $guards = [];

    /**
     * @param array|null $options
     */
    public function __construct(?array $options = null)
    {
        $options = $options ?? [];
        parent::__construct($options);

        if (isset($options['guards']) && is_array($options['guards'])) {
            $this->setGuardsConfig($options['guards']);
        }
    }

    /**
     * Gets the cached guard list or creates it from the config
     *
     * @return GuardInterface[]
     */
    public function getGuards(): array
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
     */
    protected function sortGuardsByPriority(): void
    {
        usort($this->guards, function (GuardInterface $a, GuardInterface $b) {
            return $b->getPriority() - $a->getPriority();
        });
    }

    public function getGuardsConfig(): array
    {
        return $this->guardsConfig;
    }

    public function setGuardsConfig(array $guardsConfig): void
    {
        $this->guardsConfig = $guardsConfig;
    }
}
