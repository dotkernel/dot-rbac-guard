<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 12:49 AM
 */

namespace Dot\Rbac\Guard\Options;

use Dot\Rbac\Guard\GuardInterface;
use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;

/**
 * Class RbacGuardOptions
 * @package Dot\Rbac\Guard\Options
 */
class RbacGuardOptions extends AbstractOptions
{
    const UNAUTHORIZED_EXCEPTION_MESSAGE = 0;
    const FORBIDDEN_EXCEPTION_MESSAGE = 1;

    /**
     * @var string
     */
    protected $protectionPolicy = GuardInterface::POLICY_ALLOW;

    /**
     * @var array
     */
    protected $guards = [];

    /**
     * @var bool
     */
    protected $enableRedirectForbiddenListener = false;

    /**
     * @var string|array
     */
    protected $redirectRoute;

    /**
     * @var bool
     */
    protected $allowRedirect = true;

    /**
     * @var string
     */
    protected $redirectQueryName = 'redirect';

    /** @var array  */
    protected $messages = [
        RbacGuardOptions::UNAUTHORIZED_EXCEPTION_MESSAGE => 'You must be authenticated to access this content',
        RbacGuardOptions::FORBIDDEN_EXCEPTION_MESSAGE => 'You don\'t have enough permissions to access this content',
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
    public function getGuards()
    {
        return $this->guards;
    }

    /**
     * @param array $guards
     * @return RbacGuardOptions
     */
    public function setGuards($guards)
    {
        $this->guards = $guards;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isEnableRedirectForbiddenListener()
    {
        return $this->enableRedirectForbiddenListener;
    }

    /**
     * @param boolean $enableRedirectForbiddenListener
     * @return RbacGuardOptions
     */
    public function setEnableRedirectForbiddenListener($enableRedirectForbiddenListener)
    {
        $this->enableRedirectForbiddenListener = $enableRedirectForbiddenListener;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAllowRedirect()
    {
        return $this->allowRedirect;
    }

    /**
     * @param boolean $allowRedirect
     * @return RbacGuardOptions
     */
    public function setAllowRedirect($allowRedirect)
    {
        $this->allowRedirect = $allowRedirect;
        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectQueryName()
    {
        return $this->redirectQueryName;
    }

    /**
     * @param string $redirectQueryName
     * @return RbacGuardOptions
     */
    public function setRedirectQueryName($redirectQueryName)
    {
        $this->redirectQueryName = $redirectQueryName;
        return $this;
    }

    /**
     * @return array|string
     */
    public function getRedirectRoute()
    {
        return $this->redirectRoute;
    }

    /**
     * @param array|string $redirectRoute
     * @return RbacGuardOptions
     */
    public function setRedirectRoute($redirectRoute)
    {
        $this->redirectRoute = $redirectRoute;
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
        return isset($this->messages[$key]) ? $this->messages[$key] : 'Unknown message';
    }

 }