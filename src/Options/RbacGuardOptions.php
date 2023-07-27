<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Options;

use Dot\Rbac\Guard\Guard\GuardInterface;
use Laminas\Stdlib\AbstractOptions;
use Traversable;

class RbacGuardOptions extends AbstractOptions
{
    protected string $protectionPolicy = GuardInterface::POLICY_ALLOW;

    protected array $eventListeners = [];

    protected array $guardsProvider = [];

    protected ?MessagesOptions $messagesOptions = null;

    /**
     * @param array|null|Traversable $options
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

    public function getGuardsProvider(): array
    {
        return $this->guardsProvider;
    }

    public function setGuardsProvider(array $guardsProvider): void
    {
        $this->guardsProvider = $guardsProvider;
    }

    public function getMessagesOptions(): ?MessagesOptions
    {
        if (empty($this->messagesOptions)) {
            $this->setMessagesOptions([]);
        }

        return $this->messagesOptions;
    }

    public function setMessagesOptions(array $messagesOptions): void
    {
        $this->messagesOptions = new MessagesOptions($messagesOptions);
    }

    public function getEventListeners(): array
    {
        return $this->eventListeners;
    }

    public function setEventListeners(array $eventListeners): void
    {
        $this->eventListeners = $eventListeners;
    }
}
