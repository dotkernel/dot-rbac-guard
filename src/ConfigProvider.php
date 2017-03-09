<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 12:45 AM
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard;

use Dot\Rbac\Guard\Factory\ForbiddenHandlerFactory;
use Dot\Rbac\Guard\Factory\GuardPluginManagerFactory;
use Dot\Rbac\Guard\Factory\GuardsProviderPluginManagerFactory;
use Dot\Rbac\Guard\Factory\RbacGuardMiddlewareFactory;
use Dot\Rbac\Guard\Factory\RbacGuardOptionsFactory;
use Dot\Rbac\Guard\Guard\GuardInterface;
use Dot\Rbac\Guard\Guard\GuardPluginManager;
use Dot\Rbac\Guard\Middleware\ForbiddenHandler;
use Dot\Rbac\Guard\Middleware\RbacGuardMiddleware;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Guard\Provider\GuardsProviderPluginManager;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'factories' => [
                    GuardPluginManager::class => GuardPluginManagerFactory::class,
                    GuardsProviderPluginManager::class => GuardsProviderPluginManagerFactory::class,
                    RbacGuardOptions::class => RbacGuardOptionsFactory::class,
                    RbacGuardMiddleware::class => RbacGuardMiddlewareFactory::class,
                    ForbiddenHandler::class => ForbiddenHandlerFactory::class,
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

            'dot_authorization' => [

                'protection_policy' => GuardInterface::POLICY_ALLOW,

                'guards_provider_manager' => [],

                'guard_manager' => [],

                'guards_provider' => [],

                'messages_options' => [],
            ]
        ];
    }
}
