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
use Dot\Rbac\Guard\Event\AuthorizationEvent;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Guard\RouteOptionParserTrait;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Uri;
use Zend\Expressive\Helper\UrlHelper;

/**
 * Class RedirectForbiddenListener
 * @package Dot\Rbac\Guard\Listener
 */
class RedirectForbiddenListener
{
    use RouteOptionParserTrait;

    /** @var  RbacGuardOptions */
    protected $options;

    /** @var  FlashMessengerInterface */
    protected $flashMessenger;

    /** @var  UrlHelper */
    protected $urlHelper;

    /**
     * DefaultForbiddenListener constructor.
     * @param UrlHelper $urlHelper
     * @param FlashMessengerInterface $flashMessenger
     * @param RbacGuardOptions $options
     */
    public function __construct(
        UrlHelper $urlHelper,
        FlashMessengerInterface $flashMessenger,
        RbacGuardOptions $options)
    {
        $this->urlHelper = $urlHelper;
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
        $error = $e->getParam('error', null);
        if(is_array($error)) {
            foreach ($error as $e) {
                if(is_string($e)) {
                    $messages[] = $e;
                }
            }
        }
        else if(is_string($error)) {
            $messages[] = $error;
        }
        else if($error instanceof \Exception)
        {
            $messages[] = $error->getMessage();
        }

        $messages = empty($messages)
            ? ['You don\'t have enough permissions to access this content']
            : $messages;
        
        /** @var Uri $uri */
        $uri = $this->getUri($this->options->getRedirectRoute(), $this->urlHelper);

        //add a flash message in case the login page displays errors
        if ($this->flashMessenger) {
            foreach ($messages as $message) {
                $this->flashMessenger->addError($message);
            }
        }

        $query = $uri->getQuery();
        $arr = [];
        if($this->options->isAllowRedirect()) {
            if(!empty($query)) {
                parse_str($query, $arr);
            }

            $query = http_build_query(
                array_merge($arr, [$this->options->getRedirectQueryName() => urlencode($request->getUri())])
            );

            $uri = $uri->withQuery($query);
        }

        return new RedirectResponse($uri);
    }

    /**
     * @return RbacGuardOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param RbacGuardOptions $options
     * @return RedirectForbiddenListener
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return FlashMessengerInterface
     */
    public function getFlashMessenger()
    {
        return $this->flashMessenger;
    }

    /**
     * @param FlashMessengerInterface $flashMessenger
     * @return RedirectForbiddenListener
     */
    public function setFlashMessenger(FlashMessengerInterface $flashMessenger)
    {
        $this->flashMessenger = $flashMessenger;
        return $this;
    }

    /**
     * @return UrlHelper
     */
    public function getUrlHelper()
    {
        return $this->urlHelper;
    }

    /**
     * @param UrlHelper $urlHelper
     * @return RedirectForbiddenListener
     */
    public function setUrlHelper($urlHelper)
    {
        $this->urlHelper = $urlHelper;
        return $this;
    }


}