<?php

/**
 * see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Options;

use Dot\Rbac\Guard\Guard\GuardInterface;
use Laminas\Stdlib\AbstractOptions;
use Traversable;

class RbacGuardOptions extends AbstractOptions
{
    /** @var string */
    protected $protectionPolicy = GuardInterface::POLICY_ALLOW;

    /** @var array */
    protected $eventListeners = [];

    /** @var array */
    protected $guardsProvider = [];

    /** @var  MessagesOptions */
    protected $messagesOptions;

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

    public function setProtectionPolicy(string $protectionPolicy)
    {
        $this->protectionPolicy = $protectionPolicy;
    }

    /**
     * @return array
     */
    public function getGuardsProvider(): array
    {
        return $this->guardsProvider;
    }

    /**
     * @param array $guardsProvider
     */
    public function setGuardsProvider(array $guardsProvider)
    {
        $this->guardsProvider = $guardsProvider;
    }

    public function getMessagesOptions(): MessagesOptions
    {
        if (! $this->messagesOptions) {
            $this->setMessagesOptions([]);
        }
        return $this->messagesOptions;
    }

    /**
     * @param array $messagesOptions
     */
    public function setMessagesOptions(array $messagesOptions)
    {
        $this->messagesOptions = new MessagesOptions($messagesOptions);
    }

    /**
     * @return array
     */
    public function getEventListeners(): array
    {
        return $this->eventListeners;
    }

    /**
     * @param array $eventListeners
     */
    public function setEventListeners(array $eventListeners)
    {
        $this->eventListeners = $eventListeners;
    }
}
