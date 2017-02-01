<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 12:45 AM
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class RedirectOptions
 * @package Dot\Rbac\Guard\Options
 */
class RedirectOptions extends AbstractOptions
{
    /** @var bool */
    protected $enable = false;

    /** @var  array */
    protected $redirectRoute;

    /**
     * @return boolean
     */
    public function isEnable(): bool
    {
        return $this->enable;
    }

    /**
     * @param boolean $enable
     */
    public function setEnable(bool $enable)
    {
        $this->enable = $enable;
    }

    /**
     * @return array
     */
    public function getRedirectRoute(): array
    {
        return $this->redirectRoute;
    }

    /**
     * @param array $redirectRoute
     */
    public function setRedirectRoute(array $redirectRoute)
    {
        $this->redirectRoute = $redirectRoute;
    }
}
