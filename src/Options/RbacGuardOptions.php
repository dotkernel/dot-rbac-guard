<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 5/21/2016
 * Time: 12:49 AM
 */

namespace Dot\Rbac\Guard\Options;

use Dot\Rbac\Guard\GuardInterface;
use Zend\Stdlib\AbstractOptions;

/**
 * Class RbacGuardOptions
 * @package Dot\Rbac\Guard\Options
 */
class RbacGuardOptions extends AbstractOptions
{
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


 }