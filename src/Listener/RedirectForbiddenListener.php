<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 6/18/2016
 * Time: 5:56 PM
 */

namespace Dot\Rbac\Guard\Listener;

use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Helpers\Route\RouteOptionHelper;
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
    /** @var  RbacGuardOptions */
    protected $options;

    /** @var  FlashMessengerInterface */
    protected $flashMessenger;

    /** @var  RouteOptionHelper */
    protected $routeOptionHelper;

    /**
     * DefaultForbiddenListener constructor.
     * @param RouteOptionHelper $routeOptionHelper
     * @param FlashMessengerInterface $flashMessenger
     * @param RbacGuardOptions $options
     */
    public function __construct(
        RouteOptionHelper $routeOptionHelper,
        RbacGuardOptions $options,
        FlashMessengerInterface $flashMessenger = null
    ) {
        $this->urlHelper = $routeOptionHelper;
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
        } else {
            if (is_string($error)) {
                $messages[] = $error;
            } else {
                if ($error instanceof \Exception) {
                    $messages[] = $error->getMessage();
                }
            }
        }

        $messages = empty($messages)
            ? [$this->options->getMessage(RbacGuardOptions::FORBIDDEN_EXCEPTION_MESSAGE)]
            : $messages;

        /** @var Uri $uri */
        $uri = $this->routeOptionHelper->getUri($this->options->getRedirectOptions()->getRedirectRoute());

        //add a flash message in case the landing page displays errors
        if ($this->flashMessenger) {
            foreach ($messages as $message) {
                $this->flashMessenger->addError($message);
            }
        }

        $query = $uri->getQuery();
        $arr = [];
        if ($this->options->getRedirectOptions()->isAllowRedirectParam()) {
            if (!empty($query)) {
                parse_str($query, $arr);
            }

            $query = http_build_query(
                array_merge(
                    $arr, [
                    $this->options->getRedirectOptions()->getRedirectParamName() =>
                        urlencode($request->getUri())
                ])
            );

            $uri = $uri->withQuery($query);
        }

        return new RedirectResponse($uri);
    }

}