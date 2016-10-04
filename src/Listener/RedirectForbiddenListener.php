<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 6/18/2016
 * Time: 5:56 PM
 */

namespace Dot\Rbac\Guard\Listener;

use Dot\Authorization\Exception\ForbiddenException;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Helpers\Route\RouteOptionHelper;
use Dot\Helpers\Route\UriHelperTrait;
use Dot\Rbac\Guard\Event\AuthorizationEvent;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Psr\Http\Message\ResponseInterface;
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

    /** @var bool  */
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
    public function __invoke(AuthorizationEvent $e)
    {
        $request = $e->getRequest();

        //get whatever messages
        $messages = [];
        $error = $e->getError();
        if (is_array($error)) {
            foreach ($error as $e) {
                if (is_string($e)) {
                    $messages[] = $e;
                }
            }
        } elseif (is_string($error)) {
            $messages[] = $error;
        } elseif ($error instanceof \Exception) {
            if($this->isDebug() || $error instanceof ForbiddenException) {
                $messages[] = $error->getMessage();
            }
        }

        if (empty($messages)) {
            $messages = [$this->options->getMessage(RbacGuardOptions::FORBIDDEN_MESSAGE)];
        }

        /** @var Uri $uri */
        $uri = $this->routeHelper->getUri($this->options->getRedirectOptions()->getRedirectRoute());

        //add a flash message in case the landing page displays errors
        if ($this->flashMessenger) {
            foreach ($messages as $message) {
                $this->flashMessenger->addError($message);
            }
        }

        /**
         * Append the current URI in case you want to redirect back to that after user gains permission
         */
        if ($this->options->isAllowRedirectParam()) {
            $uri = $this->appendQueryParam($uri, $request->getUri(), $this->options->getRedirectParamName());
        }

        return new RedirectResponse($uri);
    }

    /**
     * @return boolean
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @param boolean $debug
     * @return RedirectForbiddenListener
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
        return $this;
    }

}