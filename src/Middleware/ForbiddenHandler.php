<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Middleware;

use Dot\Authorization\AuthorizationInterface;
use Dot\Authorization\Exception\ForbiddenException;
use Dot\Rbac\Guard\Event\AuthorizationEvent;
use Dot\Rbac\Guard\Event\AuthorizationEventListenerInterface;
use Dot\Rbac\Guard\Event\AuthorizationEventListenerTrait;
use Dot\Rbac\Guard\Event\DispatchAuthorizationEventTrait;
use Dot\Rbac\Guard\Options\MessagesOptions;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class ForbiddenHandler
 * @package Dot\Rbac\Guard\Middleware
 */
class ForbiddenHandler implements MiddlewareInterface, AuthorizationEventListenerInterface
{
    use DispatchAuthorizationEventTrait;
    use AuthorizationEventListenerTrait;

    const TEMPLATE_DEFAULT = 'error::403';

    /** @var  AuthorizationInterface */
    protected $authorizationService;

    /** @var array */
    protected $authorizationStatusCodes = [403];

    /** @var  RbacGuardOptions */
    protected $options;

    /** @var  TemplateRendererInterface */
    protected $renderer;

    /** @var  string */
    protected $template;

    /** @var bool */
    protected $debug = false;

    /**
     * ForbiddenHandler constructor.
     * @param AuthorizationInterface $authorizationService
     * @param RbacGuardOptions $options
     * @param TemplateRendererInterface|null $templateRenderer
     * @param string|null $template
     */
    public function __construct(
        AuthorizationInterface $authorizationService,
        RbacGuardOptions $options,
        TemplateRendererInterface $templateRenderer = null,
        string $template = self::TEMPLATE_DEFAULT
    ) {
        $this->renderer = $templateRenderer;
        $this->authorizationService = $authorizationService;
        $this->options = $options;
        $this->template = $template;
    }

    /**
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return ResponseInterface
     * @throws \Exception
     * @throws \Throwable
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        try {
            $response = $delegate->process($request);
            return $response;
        } catch (ForbiddenException $e) {
            return $this->handleForbiddenError($e, $request);
        } catch (\Throwable $e) {
            if (in_array($e->getCode(), $this->authorizationStatusCodes)) {
                return $this->handleForbiddenError($e, $request);
            }
            throw $e;
        } catch (\Exception $e) {
            if (in_array($e->getCode(), $this->authorizationStatusCodes)) {
                return $this->handleForbiddenError($e, $request);
            }
            throw $e;
        }
    }

    /**
     * @param $error
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    protected function handleForbiddenError(
        $error,
        ServerRequestInterface $request
    ): ResponseInterface {
        $event = $this->dispatchEvent(AuthorizationEvent::EVENT_FORBIDDEN, [
            'request' => $request,
            'authorizationService' => $this->authorizationService,
            'error' => $error
        ]);
        if ($event instanceof ResponseInterface) {
            return $event;
        }

        $request = $event->getParam('request');
        $message = $this->options->getMessagesOptions()->getMessage(MessagesOptions::FORBIDDEN);
        if ($error instanceof ForbiddenException ||
            ($this->isDebug() && ($error instanceof \Exception || $error instanceof \Throwable))
        ) {
            $message = $error->getMessage();
        }

        // if this package is not installed within a template renderer context, re-throw the ForbiddenException
        // to be caught by the outer most error handler(default expressive handler, whoops in development)
        if (!$this->renderer) {
            throw new ForbiddenException($message);
        }

        $response = new Response();
        /** @var ResponseInterface $response */
        $response = $response->withStatus(403);
        $templateData = [
            'request' => $request,
            'uri' => $request->getUri(),
            'message' => $message,
            'status' => $response->getStatusCode(),
            'reason' => $response->getReasonPhrase()
        ];
        if ($this->isDebug()) {
            $templateData += [
                'error' => $error
            ];
        }

        return new HtmlResponse(
            $this->renderer->render($this->template, $templateData),
            403
        );
    }

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * @param bool $debug
     */
    public function setDebug(bool $debug)
    {
        $this->debug = $debug;
    }
}
