<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Middleware;

use Dot\Authentication\Exception\UnauthorizedException;
use Dot\Authorization\Exception\ForbiddenException;
use Dot\Rbac\Guard\Event\AuthorizationEvent;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Guard\GuardInterface;
use Dot\Rbac\Guard\Middleware\RbacGuardMiddleware;
use Dot\Rbac\Guard\Options\MessagesOptions;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Guard\Provider\GuardsProviderInterface;
use DotTest\Rbac\Guard\Fixture\Authentication;
use DotTest\Rbac\Guard\Fixture\AuthorizationService;
use DotTest\Rbac\Guard\Fixture\Guard;
use DotTest\Rbac\Guard\Fixture\RequestHandler;
use DotTest\Rbac\Guard\Fixture\Requests;
use Laminas\Diactoros\Response\RedirectResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class RbacGuardMiddlewareTest extends TestCase
{
    /**
     * @param list<GuardInterface> $guards
     */
    private function middleware(
        array $guards,
        string $policy = GuardInterface::POLICY_ALLOW,
        ?Authentication $authentication = null
    ): RbacGuardMiddleware {
        $provider = new class ($guards) implements GuardsProviderInterface {
            /**
             * @param list<GuardInterface> $guards
             */
            public function __construct(private readonly array $guards)
            {
            }

            public function getGuards(): array
            {
                return $this->guards;
            }
        };

        return new RbacGuardMiddleware(
            new AuthorizationService(),
            $provider,
            new RbacGuardOptions(['protection_policy' => $policy]),
            $authentication
        );
    }

    /**
     * @return array<string, array{string, list<bool>, bool}>
     */
    public static function policyProvider(): array
    {
        return [
            'allow, no guards'    => ['allow', [], true],
            'allow, all grant'    => ['allow', [true, true], true],
            'allow, first denies' => ['allow', [false, true], false],
            'allow, last denies'  => ['allow', [true, false], false],
            'deny, no guards'     => ['deny', [], false],
            'deny, all deny'      => ['deny', [false, false], false],
            'deny, first grants'  => ['deny', [true, false], true],
            'deny, last grants'   => ['deny', [false, true], true],
        ];
    }

    /**
     * @param list<bool> $results
     */
    #[DataProvider('policyProvider')]
    public function testPolicy(string $policy, array $results, bool $granted): void
    {
        $guards = [];
        foreach ($results as $result) {
            $guards[] = new Guard($result);
        }
        $handler = new RequestHandler();

        if (! $granted) {
            $this->expectException(ForbiddenException::class);
            $this->expectExceptionCode(403);
        }
        $this->middleware($guards, $policy)->process(Requests::forRoute('page'), $handler);

        $this->assertNotNull($handler->request);
    }

    public function testStopsAtFirstGuardThatDisagreesWithPolicy(): void
    {
        $first  = new Guard(false);
        $second = new Guard(true);

        try {
            $this->middleware([$first, $second])->process(Requests::forRoute('page'), new RequestHandler());
        } catch (ForbiddenException) {
        }

        $this->assertSame([1, 0], [$first->calls, $second->calls]);
    }

    public function testWithoutRouteResultIsGranted(): void
    {
        $guard   = new Guard(false);
        $handler = new RequestHandler();

        $this->middleware([$guard], 'deny')->process(Requests::withoutRouteResult(), $handler);

        $this->assertNotNull($handler->request);
        $this->assertSame(0, $guard->calls);
    }

    public function testGuestGetsUnauthorized(): void
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionCode(401);
        $this->expectExceptionMessage(
            (new MessagesOptions())->getMessage(MessagesOptions::UNAUTHORIZED)
        );

        $this->middleware([new Guard(false)], 'allow', new Authentication(false))
            ->process(Requests::forRoute('page'), new RequestHandler());
    }

    public function testIdentityGetsForbidden(): void
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage((new MessagesOptions())->getMessage(MessagesOptions::FORBIDDEN));

        $this->middleware([new Guard(false)], 'allow', new Authentication(true))
            ->process(Requests::forRoute('page'), new RequestHandler());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function eventProvider(): array
    {
        return [
            'before' => [AuthorizationEvent::EVENT_BEFORE_AUTHORIZATION],
            'after'  => [AuthorizationEvent::EVENT_AFTER_AUTHORIZATION],
        ];
    }

    #[DataProvider('eventProvider')]
    public function testListenerResponseIsReturned(string $eventName): void
    {
        $middleware = $this->middleware([new Guard(false)]);
        $response   = new RedirectResponse('/login');
        $middleware->getEventManager()->attach($eventName, static fn (): RedirectResponse => $response);

        $this->assertSame($response, $middleware->process(Requests::forRoute('page'), new RequestHandler()));
    }

    public function testAfterListenerCanGrantAccess(): void
    {
        $middleware = $this->middleware([new Guard(false)]);
        $middleware->getEventManager()->attach(
            AuthorizationEvent::EVENT_AFTER_AUTHORIZATION,
            static function (AuthorizationEvent $event): void {
                $event->setParam('authorized', true);
            }
        );
        $handler = new RequestHandler();

        $middleware->process(Requests::forRoute('page'), $handler);

        $this->assertNotNull($handler->request);
    }

    public function testBeforeListenerCanReplaceRequest(): void
    {
        $middleware = $this->middleware([]);
        $middleware->getEventManager()->attach(
            AuthorizationEvent::EVENT_BEFORE_AUTHORIZATION,
            static function (AuthorizationEvent $event): void {
                $request = $event->getParam('request');
                if ($request instanceof ServerRequestInterface) {
                    $event->setParam('request', $request->withAttribute('changed', true));
                }
            }
        );
        $handler = new RequestHandler();

        $middleware->process(Requests::forRoute('page'), $handler);

        $this->assertTrue($handler->request?->getAttribute('changed'));
    }

    public function testInvalidRequestParameterThrows(): void
    {
        $middleware = $this->middleware([]);
        $middleware->getEventManager()->attach(
            AuthorizationEvent::EVENT_BEFORE_AUTHORIZATION,
            static function (AuthorizationEvent $event): void {
                $event->setParam('request', 'invalid');
            }
        );

        $this->expectException(RuntimeException::class);

        $middleware->process(Requests::forRoute('page'), new RequestHandler());
    }
}
