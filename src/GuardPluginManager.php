<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 5/20/2016
 * Time: 8:48 PM
 */

namespace Dot\Rbac\Guard;

use Dot\Rbac\Guard\Controller\ControllerGuard;
use Dot\Rbac\Guard\Controller\ControllerPermissionGuard;
use Dot\Rbac\Guard\Factory\ControllerGuardFactory;
use Dot\Rbac\Guard\Factory\ControllerPermissionGuardFactory;
use Dot\Rbac\Guard\Factory\RouteGuardFactory;
use Dot\Rbac\Guard\Factory\RoutePermissionGuardFactory;
use Dot\Rbac\Guard\Route\RouteGuard;
use Dot\Rbac\Guard\Route\RoutePermissionGuard;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Class GuardPluginManager
 * @package Dot\Rbac\Guard
 */
class GuardPluginManager extends AbstractPluginManager
{
    protected $instanceOf = GuardInterface::class;

    protected $factories = [
        RouteGuard::class => RouteGuardFactory::class,
        RoutePermissionGuard::class => RoutePermissionGuardFactory::class,
        ControllerGuard::class => ControllerGuardFactory::class,
        ControllerPermissionGuard::class => ControllerPermissionGuardFactory::class,
    ];
}