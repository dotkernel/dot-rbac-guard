<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Factory;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\Exception\UnauthorizedException;
use Dot\Authorization\AuthorizationInterface;
use Dot\Authorization\Exception\ForbiddenException;
use Dot\Rbac\Guard\Factory\ForbiddenHandlerFactory;
use Dot\Rbac\Guard\Factory\RbacGuardMiddlewareFactory;
use Dot\Rbac\Guard\Guard\GuardPluginManager;
use Dot\Rbac\Guard\Middleware\ForbiddenHandler;
use Dot\Rbac\Guard\Middleware\RbacGuardMiddleware;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Guard\Provider\GuardsProviderPluginManager;
use Dot\Rbac\Role\RoleServiceInterface;
use DotTest\Rbac\Guard\Fixture\Authentication;
use DotTest\Rbac\Guard\Fixture\AuthorizationListener;
use DotTest\Rbac\Guard\Fixture\AuthorizationService;
use DotTest\Rbac\Guard\Fixture\InMemoryContainer;
use DotTest\Rbac\Guard\Fixture\RequestHandler;
use DotTest\Rbac\Guard\Fixture\Requests;
use DotTest\Rbac\Guard\Fixture\RoleService;
use DotTest\Rbac\Guard\Fixture\TemplateRenderer;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MiddlewareFactoriesTest extends TestCase
{
    /**
     * @param array<string, mixed> $config
     * @param array<array-key, mixed> $authorization
     */
    private function container(array $config = [], array $authorization = []): InMemoryContainer
    {
        $container = new InMemoryContainer([
            'config'                      => $config,
            AuthorizationInterface::class => new AuthorizationService(),
            RoleServiceInterface::class   => new RoleService(['user']),
            RbacGuardOptions::class       => new RbacGuardOptions($authorization),
        ]);
        $container->set(GuardPluginManager::class, new GuardPluginManager($container));
        $container->set(GuardsProviderPluginManager::class, new GuardsProviderPluginManager($container));

        return $container;
    }

    /**
     * @return array<string, array{array<string, mixed>, bool, string}>
     */
    public static function forbiddenHandlerConfigProvider(): array
    {
        return [
            'empty config'        => [[], false, ForbiddenHandler::TEMPLATE_DEFAULT],
            'debug true'          => [['debug' => true], true, ForbiddenHandler::TEMPLATE_DEFAULT],
            'debug not a boolean' => [['debug' => 'yes'], false, ForbiddenHandler::TEMPLATE_DEFAULT],
            'custom template'     => [
                ['mezzio' => ['error_handler' => ['template_403' => 'app::403']]],
                false,
                'app::403',
            ],
            'empty template'      => [
                ['mezzio' => ['error_handler' => ['template_403' => '']]],
                false,
                ForbiddenHandler::TEMPLATE_DEFAULT,
            ],
            'non-string template' => [
                ['mezzio' => ['error_handler' => ['template_403' => 403]]],
                false,
                ForbiddenHandler::TEMPLATE_DEFAULT,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $config
     */
    #[DataProvider('forbiddenHandlerConfigProvider')]
    public function testForbiddenHandlerFactory(array $config, bool $debug, string $template): void
    {
        $renderer  = new TemplateRenderer();
        $container = $this->container($config);
        $container->set(TemplateRendererInterface::class, $renderer);

        $handler = (new ForbiddenHandlerFactory())($container, ForbiddenHandler::class);
        $this->assertSame($debug, $handler->isDebug());

        $handler->process(Requests::withoutRouteResult(), new RequestHandler(static function (): never {
            throw new ForbiddenException('denied');
        }));
        $this->assertSame($template, $renderer->lastTemplate);
    }

    public function testForbiddenHandlerFactoryAttachesListeners(): void
    {
        $listener  = new AuthorizationListener();
        $container = $this->container([], ['event_listeners' => ['listener']]);
        $container->set('listener', $listener);

        $handler = (new ForbiddenHandlerFactory())($container, ForbiddenHandler::class);

        try {
            $handler->process(Requests::withoutRouteResult(), new RequestHandler(static function (): never {
                throw new ForbiddenException('denied');
            }));
        } catch (ForbiddenException) {
            // no renderer: the handler throws the exception again
        }
        $this->assertSame(['event.authorization.forbidden'], $listener->received);
    }

    public function testRbacGuardMiddlewareFactory(): void
    {
        $container = $this->container([], [
            'protection_policy' => 'deny',
            'guards_provider'   => [
                'type'    => 'ArrayGuards',
                'options' => [
                    'guards' => [
                        ['type' => 'Route', 'options' => ['rules' => ['home' => ['*'], 'admin' => ['admin']]]],
                    ],
                ],
            ],
        ]);

        $middleware = (new RbacGuardMiddlewareFactory())($container, RbacGuardMiddleware::class);

        $handler = new RequestHandler();
        $middleware->process(Requests::forRoute('home'), $handler);
        $this->assertNotNull($handler->request);

        $this->expectException(ForbiddenException::class);
        $middleware->process(Requests::forRoute('admin'), $handler);
    }

    public function testRbacGuardMiddlewareFactoryUsesAuthentication(): void
    {
        $container = $this->container([], [
            'protection_policy' => 'deny',
            'guards_provider'   => ['type' => 'ArrayGuards', 'options' => ['guards' => []]],
        ]);
        $container->set(AuthenticationInterface::class, new Authentication(false));

        $middleware = (new RbacGuardMiddlewareFactory())($container, RbacGuardMiddleware::class);

        $this->expectException(UnauthorizedException::class);
        $middleware->process(Requests::forRoute('home'), new RequestHandler());
    }
}
