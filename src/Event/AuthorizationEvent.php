<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 6/14/2016
 * Time: 5:27 PM
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Event;

use Dot\Authorization\AuthorizationInterface;
use Dot\Event\Event;

/**
 * Class AuthorizationEvent
 * @package Dot\Rbac\Guard\Event
 */
class AuthorizationEvent extends Event
{
    const EVENT_AUTHORIZE = 'event.authorization.authorize';
    const EVENT_FORBIDDEN = 'event.authorization.forbidden';

    /** @var bool */
    protected $authorized = false;

    /** @var  AuthorizationInterface */
    protected $authorizationService;

    /** @var  mixed */
    protected $error;

    /**
     * @return boolean
     */
    public function isAuthorized() : bool
    {
        return $this->authorized;
    }

    /**
     * @param boolean $authorized
     */
    public function setAuthorized(bool $authorized)
    {
        $this->authorized = $authorized;
    }

    /**
     * @return AuthorizationInterface
     */
    public function getAuthorizationService() : AuthorizationInterface
    {
        return $this->authorizationService;
    }

    /**
     * @param AuthorizationInterface $authorizationService
     */
    public function setAuthorizationService(AuthorizationInterface $authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }

    /**
     * @return mixed
     */
    public function getError() : ?mixed
    {
        return $this->error;
    }

    /**
     * @param mixed $error
     */
    public function setError(mixed $error)
    {
        $this->error = $error;
    }
}
