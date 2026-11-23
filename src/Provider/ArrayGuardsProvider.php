<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Guard\GuardInterface;
use Psr\Container\ContainerExceptionInterface;

use function is_array;
use function usort;

class ArrayGuardsProvider extends AbstractGuardsProvider
{
    /** @var array<array-key, mixed> */
    protected array $guardsConfig = [];

    /** @var list<GuardInterface> */
    protected array $guards = [];

    /**
     * @param array<array-key, mixed>|null $options
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
     * @return list<GuardInterface>
     * @throws ContainerExceptionInterface
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
            if (! is_array($guardConfig)) {
                throw new RuntimeException('Each guard config must be an array with the keys "type" and "options"');
            }
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
        usort(
            $this->guards,
            static fn (GuardInterface $a, GuardInterface $b): int => $b->getPriority() <=> $a->getPriority()
        );
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getGuardsConfig(): array
    {
        return $this->guardsConfig;
    }

    /**
     * @param array<array-key, mixed> $guardsConfig
     */
    public function setGuardsConfig(array $guardsConfig): void
    {
        $this->guardsConfig = $guardsConfig;
    }
}
