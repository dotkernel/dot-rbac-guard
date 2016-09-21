<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 12:45 AM
 */

namespace Dot\Rbac\Guard\Options;


use Zend\Stdlib\AbstractOptions;

/**
 * Class RedirectOptions
 * @package Dot\Rbac\Guard\Options
 */
class RedirectOptions extends AbstractOptions
{
    /** @var bool  */
    protected $enable = false;

    /** @var  array */
    protected $redirectRoute;

    /** @var bool  */
    protected $allowRedirectParam = true;

    /** @var string  */
    protected $redirectParamName = 'redirect';

    /**
     * @return boolean
     */
    public function isEnable()
    {
        return $this->enable;
    }

    /**
     * @param boolean $enable
     * @return RedirectOptions
     */
    public function setEnable($enable)
    {
        $this->enable = $enable;
        return $this;
    }

    /**
     * @return array
     */
    public function getRedirectRoute()
    {
        return $this->redirectRoute;
    }

    /**
     * @param array $redirectRoute
     * @return RedirectOptions
     */
    public function setRedirectRoute($redirectRoute)
    {
        $this->redirectRoute = $redirectRoute;
        return $this;
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
     * @return RedirectOptions
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
     * @return RedirectOptions
     */
    public function setRedirectParamName($redirectParamName)
    {
        $this->redirectParamName = $redirectParamName;
        return $this;
    }


}