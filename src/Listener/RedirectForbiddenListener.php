<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 6/18/2016
 * Time: 5:56 PM
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Listener;

use Dot\Authorization\Exception\ForbiddenException;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Helpers\Route\RouteOptionHelper;
use Dot\Helpers\Route\UriHelperTrait;
use Dot\Rbac\Guard\Event\AuthorizationEvent;
use Dot\Rbac\Guard\Exception\RuntimeException;
use Dot\Rbac\Guard\Options\MessagesOptions;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Uri;

/**
 * Class RedirectForbiddenListener
 * @package Dot\Rbac\Guard\Listener
 */
class RedirectForbiddenListener
{
    use UriHelperTrait;

    /** @var  RbacGuardOptions */
    protected $options;

    /** @var  FlashMessengerInterface */
    protected $flashMessenger;

    /** @var  RouteOptionHelper */
    protected $routeHelper;

    /** @var bool */
    protected $debug = false;

    /**
     * DefaultForbiddenListener constructor.
     * @param RouteOptionHelper $routeHelper
     * @param FlashMessengerInterface $flashMessenger
     * @param RbacGuardOptions $options
     */
    public function __construct(
        RouteOptionHelper $routeHelper,
        RbacGuardOptions $options,
        FlashMessengerInterface $flashMessenger = null
    ) {
        $this->routeHelper = $routeHelper;
        $this->flashMessenger = $flashMessenger;
        $this->options = $options;
    }

    /**
     * @param AuthorizationEvent $e
     * @return ResponseInterface
     */
    public function __invoke(AuthorizationEvent $e): ResponseInterface
    {
        $request = $e->getRequest();

        $messages = $this->getErrorMessages($e);
        if (empty($messages)) {
            $messages = [$this->options->getMessagesOptions()->getMessage(MessagesOptions::FORBIDDEN)];
        }

        /** @var Uri $uri */
        $uri = $this->routeHelper->getUri($this->options->getRedirectOptions()->getRedirectRoute());
        if ($this->areUriEqual($uri, $request->getUri())) {
            throw new RuntimeException('The forbidden redirection route is the same as the forbidden route' .
                ' This can result in an endless redirect loop.' .
                ' Please edit your  authorization schema to open the route you want to redirect to');
        }

        //add a flash message in case the landing page displays errors
        if ($this->flashMessenger) {
            $this->flashMessenger->addError($messages);
        }

        /**
         * Append the current URI in case you want to redirect back to that after user gains permission
         */
        if ($this->options->isEnableWantedUrl()) {
            $uri = $this->appendQueryParam(
                $uri,
                $this->options->getWantedUrlName(),
                $request->getUri()->__toString()
            );
        }

        return new RedirectResponse($uri);
    }

    /**
     * @param AuthorizationEvent $e
     * @return array
     */
    public function getErrorMessages(AuthorizationEvent $e): array
    {
        //get whatever messages
        $messages = [];
        $error = $e->getError();
        if (is_array($error) || is_string($error)) {
            $error = (array)$error;
            foreach ($error as $e) {
                if (is_string($e)) {
                    $messages[] = $e;
                }
            }
        } elseif ($error instanceof \Exception) {
            if ($this->isDebug() || $error instanceof ForbiddenException) {
                $messages[] = $error->getMessage();
            }
        }
        return $messages;
    }

    /**
     * @return boolean
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * @param boolean $debug
     */
    public function setDebug(bool $debug)
    {
        $this->debug = $debug;
    }

    /**
     * @param UriInterface $uri1
     * @param UriInterface $uri2
     * @return bool
     */
    protected function areUriEqual(UriInterface $uri1, UriInterface $uri2): bool
    {
        return $uri1->getScheme() === $uri2->getScheme()
            && $uri1->getHost() === $uri2->getHost()
            && $uri1->getPath() === $uri2->getPath()
            && $uri1->getPort() === $uri2->getPort();
    }
}
