<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 12:45 AM
 */

namespace Dot\Rbac\Guard;

use Dot\Rbac\Guard\Factory\DefaultAuthorizationListenerFactory;
use Dot\Rbac\Guard\Factory\ForbiddenHandlerFactory;
use Dot\Rbac\Guard\Factory\GuardPluginManagerFactory;
use Dot\Rbac\Guard\Factory\GuardsProviderFactory;
use Dot\Rbac\Guard\Factory\RbacGuardMiddlewareFactory;
use Dot\Rbac\Guard\Factory\RbacGuardOptionsFactory;
use Dot\Rbac\Guard\Factory\RedirectForbiddenListenerFactory;
use Dot\Rbac\Guard\Listener\DefaultAuthorizationListener;
use Dot\Rbac\Guard\Listener\RedirectForbiddenListener;
use Dot\Rbac\Guard\Middleware\ForbiddenHandler;
use Dot\Rbac\Guard\Middleware\RbacGuardMiddleware;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Guard\Provider\GuardsProviderInterface;

class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dependencies' => [
                'factories' => [
                    GuardPluginManager::class => GuardPluginManagerFactory::class,
                    RbacGuardOptions::class => RbacGuardOptionsFactory::class,
                    GuardsProviderInterface::class => GuardsProviderFactory::class,

                    RbacGuardMiddleware::class => RbacGuardMiddlewareFactory::class,
                    ForbiddenHandler::class => ForbiddenHandlerFactory::class,

                    RedirectForbiddenListener::class => RedirectForbiddenListenerFactory::class,

                    DefaultAuthorizationListener::class => DefaultAuthorizationListenerFactory::class,
                ],
            ],

            'middleware_pipeline' => [
                'error' => [
                    'middleware' => [
                        ForbiddenHandler::class,
                    ],
                    'error' => true,
                    'priority' => -10000,
                ],
            ],

            'dk_authorization' => [

                'protection_policy' => GuardInterface::POLICY_ALLOW,

                'guards' => [

                ],

                'guard_manager' => [
                    'factories' => [

                    ],
                    'invokables' => [

                    ]
                ],
            ]
        ];
    }
}