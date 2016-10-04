<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 12:49 AM
 */

namespace Dot\Rbac\Guard\Options;

use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Dot\Rbac\Guard\GuardInterface;
use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;

/**
 * Class RbacGuardOptions
 * @package Dot\Rbac\Guard\Options
 */
class RbacGuardOptions extends AbstractOptions
{
    const UNAUTHORIZED_MESSAGE = 0;
    const FORBIDDEN_MESSAGE = 1;

    /**
     * @var string
     */
    protected $protectionPolicy = GuardInterface::POLICY_ALLOW;

    /** @var array  */
    protected $guardsProvider = [];

    /** @var bool  */
    protected $allowRedirectParam = true;

    /** @var string  */
    protected $redirectParamName = 'redirect';

    /** @var  RedirectOptions */
    protected $redirectOptions;

    /** @var array  */
    protected $messages = [
        RbacGuardOptions::UNAUTHORIZED_MESSAGE => 'You must be authenticated to access this content',
        RbacGuardOptions::FORBIDDEN_MESSAGE => 'You don\'t have enough permissions to access this content',
    ];

    /**
     * ModuleOptions constructor.
     * @param array|null|\Traversable $options
     */
    public function __construct($options)
    {
        $this->__strictMode__ = false;
        parent::__construct($options);
    }

    /**
     * @return string
     */
    public function getProtectionPolicy()
    {
        return $this->protectionPolicy;
    }

    /**
     * @param string $protectionPolicy
     * @return RbacGuardOptions
     */
    public function setProtectionPolicy($protectionPolicy)
    {
        $this->protectionPolicy = $protectionPolicy;
        return $this;
    }

    /**
     * @return array
     */
    public function getGuardsProvider()
    {
        return $this->guardsProvider;
    }

    /**
     * @param array $guardsProvider
     * @return RbacGuardOptions
     */
    public function setGuardsProvider($guardsProvider)
    {
        $this->guardsProvider = $guardsProvider;
        return $this;
    }

    /**
     * @return RedirectOptions
     */
    public function getRedirectOptions()
    {
        return $this->redirectOptions;
    }

    /**
     * @param RedirectOptions|array $redirectOptions
     * @return RbacGuardOptions
     */
    public function setRedirectOptions($redirectOptions)
    {
        if(is_array($redirectOptions)) {
            $this->redirectOptions = new RedirectOptions($redirectOptions);
        }
        elseif($redirectOptions instanceof RedirectOptions) {
            $this->redirectOptions = $redirectOptions;
        }
        else {
            throw new InvalidArgumentException('Redirect options must be an array or an instance of ' .
                RedirectOptions::class);
        }

        return $this;
    }


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

    /**
     * @return boolean
     */
    public function isAllowRedirectParam()
    {
        return $this->allowRedirectParam;
    }

    /**
     * @param boolean $allowRedirectParam
     * @return RbacGuardOptions
     */
    public function setAllowRedirectParam($allowRedirectParam)
    {
        $this->allowRedirectParam = $allowRedirectParam;
        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectParamName()
    {
        return $this->redirectParamName;
    }

    /**
     * @param string $redirectParamName
     * @return RbacGuardOptions
     */
    public function setRedirectParamName($redirectParamName)
    {
        $this->redirectParamName = $redirectParamName;
        return $this;
    }
 }