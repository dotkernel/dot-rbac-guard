<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 10/10/2016
 * Time: 8:01 PM
 */

namespace Dot\Rbac\Guard\Options;

use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;

class MessagesOptions extends AbstractOptions
{
    const UNAUTHORIZED_MESSAGE = 0;
    const FORBIDDEN_MESSAGE = 1;

    /** @var array */
    protected $messages = [
        MessagesOptions::UNAUTHORIZED_MESSAGE => 'You must be authenticated to access the requested content',
        MessagesOptions::FORBIDDEN_MESSAGE => 'You don\'t have enough permissions to access the requested content',
    ];

    protected $__strictMode__ = false;

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param $messages
     * @return $this
     */
    public function setMessages($messages)
    {
        $this->messages = ArrayUtils::merge($this->messages, $messages, true);
        return $this;
    }

    /**
     * @param $key
     * @return mixed|string
     */
    public function getMessage($key)
    {
        return isset($this->messages[$key]) ? $this->messages[$key] : null;
    }
}
