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

    /** @var  AuthorizationInterface */
    protected $authorizationService;

    /** @var array */
    protected $authorizationStatusCodes = [403];

    /** @var  RbacGuardOptions */
    protected $options;

    /** @var  TemplateRendererInterface */
    protected $renderer;

    /** @var bool  */
    protected $debug = false;

    /**
     * ForbiddenHandler constructor.
     * @param AuthorizationInterface $authorizationService
     * @param TemplateRendererInterface $templateRenderer
     * @param RbacGuardOptions $options
     */
    public function __construct(
        AuthorizationInterface $authorizationService,
        RbacGuardOptions $options,
        TemplateRendererInterface $templateRenderer = null
    ) {
        $this->renderer = $templateRenderer;
        $this->authorizationService = $authorizationService;
        $this->options = $options;
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

        $message = $this->options->getMessagesOptions()->getMessage(MessagesOptions::FORBIDDEN);
        if ($error instanceof ForbiddenException) {
            $message = $error->getMessage();
        }

        if (empty($this->options->getForbiddenTemplateName()) || empty($this->renderer)) {
            throw new ForbiddenException($message);
        }

        return new HtmlResponse($this->renderer->render($this->options->getForbiddenTemplateName(), [
            'request' => $request,
            'error' => $error,
            'isDebug' => $this->isDebug(),
            'message' => $message
        ]), 403);
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
