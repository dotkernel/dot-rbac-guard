<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 6/14/2016
 * Time: 5:27 PM
 */

namespace Dot\Rbac\Guard\Event;

use Dot\Authorization\AuthorizationInterface;
use Dot\Event\Event;

/**
 * Class AuthorizationEvent
 * @package Dot\Rbac\Guard\Event
 */
class AuthorizationEvent extends Event
{
    const EVENT_AUTHORIZE = 'authorize';
    const EVENT_FORBIDDEN = 'forbidden';

    /** @var bool  */
    protected $authorized = false;

    /** @var  AuthorizationInterface */
    protected $authorizationService;

    /** @var  mixed */
    protected $error;

    /**
     * @return boolean
     */
    public function isAuthorized()
    {
        return $this->authorized;
    }

    /**
     * @param boolean $authorized
     * @return AuthorizationEvent
     */
    public function setAuthorized($authorized)
    {
        $this->authorized = $authorized;
        return $this;
    }

    /**
     * @return AuthorizationInterface
     */
    public function getAuthorizationService()
    {
        return $this->authorizationService;
    }

    /**
     * @param AuthorizationInterface $authorizationService
     * @return AuthorizationEvent
     */
    public function setAuthorizationService(AuthorizationInterface $authorizationService)
    {
        $this->authorizationService = $authorizationService;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param mixed $error
     * @return AuthorizationEvent
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }


    
}