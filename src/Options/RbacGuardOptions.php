<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Options;

use Dot\Rbac\Guard\Guard\GuardInterface;
use Laminas\Stdlib\AbstractOptions;
use Laminas\Stdlib\ParameterObjectInterface;
use Traversable;

/**
 * @extends AbstractOptions<ParameterObjectInterface>
 */
class RbacGuardOptions extends AbstractOptions
{
    protected string $protectionPolicy = GuardInterface::POLICY_ALLOW;

    /** @var array<array-key, mixed> */
    protected array $eventListeners = [];

    /** @var array<array-key, mixed> */
    protected array $guardsProvider = [];

    protected ?MessagesOptions $messagesOptions = null;

    /**
     * @param array<string, mixed>|null|Traversable<string, mixed> $options
     */
    public function __construct($options)
    {
        $this->__strictMode__ = false;
        parent::__construct($options);
    }

    public function getProtectionPolicy(): string
    {
        return $this->protectionPolicy;
    }

    public function setProtectionPolicy(string $protectionPolicy): void
    {
        $this->protectionPolicy = $protectionPolicy;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getGuardsProvider(): array
    {
        return $this->guardsProvider;
    }

    /**
     * @param array<array-key, mixed> $guardsProvider
     */
    public function setGuardsProvider(array $guardsProvider): void
    {
        $this->guardsProvider = $guardsProvider;
    }

    public function getMessagesOptions(): MessagesOptions
    {
        return $this->messagesOptions ??= new MessagesOptions([]);
    }

    /**
     * @param array<string, mixed> $messagesOptions
     */
    public function setMessagesOptions(array $messagesOptions): void
    {
        $this->messagesOptions = new MessagesOptions($messagesOptions);
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getEventListeners(): array
    {
        return $this->eventListeners;
    }

    /**
     * @param array<array-key, mixed> $eventListeners
     */
    public function setEventListeners(array $eventListeners): void
    {
        $this->eventListeners = $eventListeners;
    }
}
