<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Guard;

use Dot\Authorization\AuthorizationInterface;
use Dot\Controller\AbstractController;
use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Psr\Http\Message\ServerRequestInterface;
use Mezzio\Router\RouteResult;

/**
 * Class ControllerPermissionGuard
 * @package Dot\Rbac\Guard\Controller
 */
class ControllerPermissionGuard extends AbstractGuard
{
    const PRIORITY = 10;

    /** @var AuthorizationInterface */
    protected $authorizationService;

    /**
     * ControllerPermissionGuard constructor.
     * @param array $options
     */
    public function __construct(array $options = null)
    {
        $options = $options ?? [];
        parent::__construct($options);

        if (isset($options['authorization_service'])
            && $options['authorization_service'] instanceof AuthorizationInterface
        ) {
            $this->setAuthorizationService($options['authorization_service']);
        }

        if (!$this->authorizationService instanceof AuthorizationInterface) {
            throw new RuntimeException('Authorization service is required by this guard and was not set');
        }
    }

    /**
     * @param array $rules
     */
    public function setRules(array $rules)
    {
        $this->rules = [];

        foreach ($rules as $rule) {
            $route = strtolower($rule['route']);
            $actions = isset($rule['actions']) ? (array)$rule['actions'] : [];
            $permissions = (array)$rule['permissions'];

            if (empty($actions)) {
                $this->rules[$route][0] = $permissions;
                continue;
            }

            foreach ($actions as $action) {
                $action = AbstractController::getMethodFromAction($action);
                $this->rules[$route][$action] = $permissions;
            }
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function isGranted(ServerRequestInterface $request): bool
    {
        $routeResult = $request->getAttribute(RouteResult::class, null);
        if (!$routeResult instanceof RouteResult || !$routeResult->getMatchedRouteName()) {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        $route = $routeResult->getMatchedRouteName();

        if (!isset($this->rules[$route])) {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        $params = $routeResult->getMatchedParams();
        $action = isset($params['action']) && !empty($params['action'])
            ? $params['action']
            : 'index';

        $action = AbstractController::getMethodFromAction($action);

        if (isset($this->rules[$route][$action])) {
            $allowedPermissions = $this->rules[$route][$action];
        } elseif (isset($this->rules[$route][0])) {
            $allowedPermissions = $this->rules[$route][0];
        } else {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        if (empty($allowedPermissions)) {
            return $this->protectionPolicy === self::POLICY_ALLOW;
        }

        if (in_array('*', $allowedPermissions)) {
            return true;
        }

        $permissions = $allowedPermissions['permissions'] ?? $allowedPermissions;
        $condition = $allowedPermissions['condition'] ?? GuardInterface::CONDITION_AND;

        if (GuardInterface::CONDITION_AND === $condition) {
            foreach ($permissions as $permission) {
                if (!$this->getAuthorizationService()->isGranted($permission)) {
                    return false;
                }
            }
            return true;
        }

        if (GuardInterface::CONDITION_OR === $condition) {
            foreach ($permissions as $permission) {
                if ($this->getAuthorizationService()->isGranted($permission)) {
                    return true;
                }
            }
            return false;
        }

        throw new InvalidArgumentException(sprintf(
            'Condition must be either "AND" or "OR", %s given',
            is_object($condition) ? get_class($condition) : gettype($condition)
        ));
    }

    /**
     * @return AuthorizationInterface
     */
    public function getAuthorizationService(): AuthorizationInterface
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
}
