<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Options;

use Laminas\Stdlib\AbstractOptions;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\ParameterObjectInterface;
use Traversable;

/**
 * @extends AbstractOptions<ParameterObjectInterface>
 */
class MessagesOptions extends AbstractOptions
{
    public const UNAUTHORIZED = 0;
    public const FORBIDDEN    = 1;

    /** @var array<int, string> */
    protected array $messages = [
        self::UNAUTHORIZED => 'You must sign in first in order to access the requested content',
        self::FORBIDDEN    => 'You don\'t have enough permissions to access the requested content',
    ];

    /**
     * @param array<string, mixed>|null|Traversable<string, mixed> $options
     */
    public function __construct($options = null)
    {
        $this->__strictMode__ = false;
        parent::__construct($options);
    }

    /**
     * @return array<int, string>
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param array<int, string> $messages
     */
    public function setMessages(array $messages): void
    {
        $this->messages = ArrayUtils::merge($this->messages, $messages, true);
    }

    public function getMessage(int $key): string
    {
        return $this->messages[$key] ?? '';
    }
}
