<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 10/10/2016
 * Time: 8:01 PM
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Options;

use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;

class MessagesOptions extends AbstractOptions
{
    const UNAUTHORIZED = 0;
    const FORBIDDEN = 1;

    /** @var array */
    protected $messages = [
        MessagesOptions::UNAUTHORIZED => 'You have to sign in first to access the requested content',
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
    public function getMessages() : array
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
     * @param string $key
     * @return string
     */
    public function getMessage(string $key) : string
    {
        return $this->messages[$key] ?? '';
    }
}
