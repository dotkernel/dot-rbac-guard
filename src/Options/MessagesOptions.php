<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Options;

use Laminas\Stdlib\AbstractOptions;
use Laminas\Stdlib\ArrayUtils;

class MessagesOptions extends AbstractOptions
{
    const UNAUTHORIZED = 0;
    const FORBIDDEN = 1;

    /** @var array */
    protected $messages = [
        MessagesOptions::UNAUTHORIZED => 'You must sign in first in order to access the requested content',
        MessagesOptions::FORBIDDEN => 'You don\'t have enough permissions to access the requested content',
    ];

    /**
     * MessagesOptions constructor.
     * @param null $options
     */
    public function __construct($options = null)
    {
        $this->__strictMode__ = false;
        parent::__construct($options);
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param $messages
     */
    public function setMessages(array $messages)
    {
        $this->messages = ArrayUtils::merge($this->messages, $messages, true);
    }

    /**
     * @param int $key
     * @return string
     */
    public function getMessage(int $key): string
    {
        return $this->messages[$key] ?? '';
    }
}
