<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Middleware;

use Dot\Authorization\Exception\ForbiddenException;
use Dot\Rbac\Guard\Event\AuthorizationEvent;
use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Middleware\ForbiddenHandler;
use Dot\Rbac\Guard\Options\MessagesOptions;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use DotTest\Rbac\Guard\Fixture\AuthorizationService;
use DotTest\Rbac\Guard\Fixture\RequestHandler;
use DotTest\Rbac\Guard\Fixture\Requests;
use DotTest\Rbac\Guard\Fixture\TemplateRenderer;
use Exception;
use Laminas\Diactoros\Response\TextResponse;
use PHPUnit\Framework\TestCase;

class ForbiddenHandlerTest extends TestCase
{
    private function handler(?TemplateRenderer $renderer = null, string $template = 'error::403'): ForbiddenHandler
    {
        return new ForbiddenHandler(new AuthorizationService(), new RbacGuardOptions([]), $renderer, $template);
    }

    private function throwing(Exception $exception): RequestHandler
    {
        return new RequestHandler(static function () use ($exception): never {
            throw $exception;
        });
    }

    public function testEmptyTemplateThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The 403 template name must not be empty');

        $this->handler(null, '');
    }

    public function testPassesResponseThrough(): void
    {
        $result = $this->handler()->process(Requests::withoutRouteResult(), new RequestHandler());

        $this->assertInstanceOf(TextResponse::class, $result);
    }

    public function testOtherExceptionIsThrownAgain(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Exception');

        $this->handler(new TemplateRenderer())->process(
            Requests::withoutRouteResult(),
            $this->throwing(new Exception('Exception', 500))
        );
    }

    public function testWithoutRendererForbiddenExceptionIsThrownAgain(): void
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('ForbiddenException');

        $this->handler()->process(
            Requests::withoutRouteResult(),
            $this->throwing(new ForbiddenException('ForbiddenException'))
        );
    }

    public function testRendersTemplateForForbiddenException(): void
    {
        $renderer = new TemplateRenderer();
        $response = $this->handler($renderer, 'app::403')->process(
            Requests::withoutRouteResult(),
            $this->throwing(new ForbiddenException('No access'))
        );

        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame('app::403: No access', (string) $response->getBody());
        $this->assertSame('Forbidden', $renderer->lastParams['reason']);
        $this->assertArrayNotHasKey('error', $renderer->lastParams);
    }

    public function testCode403UsesDefaultMessageWithoutDebug(): void
    {
        $renderer = new TemplateRenderer();
        $response = $this->handler($renderer)->process(
            Requests::withoutRouteResult(),
            $this->throwing(new Exception('internal detail', 403))
        );

        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame(
            'error::403: ' . (new MessagesOptions())->getMessage(MessagesOptions::FORBIDDEN),
            (string) $response->getBody()
        );
    }

    public function testCode403UsesExceptionMessageWithDebug(): void
    {
        $renderer = new TemplateRenderer();
        $handler  = $this->handler($renderer);
        $handler->setDebug(true);
        $error = new Exception('internal detail', 403);

        $response = $handler->process(Requests::withoutRouteResult(), $this->throwing($error));

        $this->assertTrue($handler->isDebug());
        $this->assertSame('error::403: internal detail', (string) $response->getBody());
        $this->assertSame($error, $renderer->lastParams['error']);
    }

    public function testListenerResponseIsReturned(): void
    {
        $handler  = $this->handler(new TemplateRenderer());
        $response = new TextResponse('custom', 403);
        $handler->getEventManager()->attach(
            AuthorizationEvent::EVENT_FORBIDDEN,
            static fn (): TextResponse => $response
        );

        $result = $handler->process(Requests::withoutRouteResult(), $this->throwing(new ForbiddenException('no')));

        $this->assertSame($response, $result);
    }

    public function testInvalidRequestParameterThrows(): void
    {
        $handler = $this->handler(new TemplateRenderer());
        $handler->getEventManager()->attach(
            AuthorizationEvent::EVENT_FORBIDDEN,
            static function (AuthorizationEvent $event): void {
                $event->setParam('request', null);
            }
        );

        $this->expectException(RuntimeException::class);

        $handler->process(Requests::withoutRouteResult(), $this->throwing(new ForbiddenException('no')));
    }
}
