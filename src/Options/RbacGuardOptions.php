<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 12:49 AM
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Options;

use Dot\Rbac\Guard\Guard\GuardInterface;
use Zend\Stdlib\AbstractOptions;

/**
 * Class RbacGuardOptions
 * @package Dot\Rbac\Guard\Options
 */
class RbacGuardOptions extends AbstractOptions
{
    /**
     * @var string
     */
    protected $protectionPolicy = GuardInterface::POLICY_ALLOW;

    /** @var array */
    protected $guardsProvider = [];

    /** @var bool */
    protected $enableWantedUrl = true;

    /** @var string */
    protected $wantedUrlName = 'redirect';

    /** @var  RedirectOptions */
    protected $redirectOptions;

    /** @var  MessagesOptions */
    protected $messagesOptions;

    /**
     * ModuleOptions constructor.
     * @param array|null|\Traversable $options
     */
    public function __construct($options)
    {
        $this->__strictMode__ = false;
        parent::__construct($options);
    }

    /**
     * @return string
     */
    public function getProtectionPolicy() : string
    {
        return $this->protectionPolicy;
    }

    /**
     * @param string $protectionPolicy
     */
    public function setProtectionPolicy(string $protectionPolicy)
    {
        $this->protectionPolicy = $protectionPolicy;
    }

    /**
     * @return array
     */
    public function getGuardsProvider() : array
    {
        return $this->guardsProvider;
    }

    /**
     * @param array $guardsProvider
     */
    public function setGuardsProvider(array $guardsProvider)
    {
        $this->guardsProvider = $guardsProvider;
    }

    /**
     * @return RedirectOptions
     */
    public function getRedirectOptions() : RedirectOptions
    {
        if (!$this->redirectOptions) {
            $this->setRedirectOptions([]);
        }
        return $this->redirectOptions;
    }

    /**
     * @param array $redirectOptions
     */
    public function setRedirectOptions(array $redirectOptions)
    {
        $this->redirectOptions = new RedirectOptions($redirectOptions);
    }

    /**
     * @return MessagesOptions
     */
    public function getMessagesOptions() : MessagesOptions
    {
        if (!$this->messagesOptions) {
            $this->setMessagesOptions([]);
        }
        return $this->messagesOptions;
    }

    /**
     * @param array $messagesOptions
     */
    public function setMessagesOptions(array $messagesOptions)
    {
        $this->messagesOptions = new MessagesOptions($messagesOptions);
    }

    /**
     * @return bool
     */
    public function isEnableWantedUrl(): bool
    {
        return $this->enableWantedUrl;
    }

    /**
     * @param bool $enableWantedUrl
     */
    public function setEnableWantedUrl(bool $enableWantedUrl)
    {
        $this->enableWantedUrl = $enableWantedUrl;
    }

    /**
     * @return string
     */
    public function getWantedUrlName(): string
    {
        return $this->wantedUrlName;
    }

    /**
     * @param string $wantedUrlName
     */
    public function setWantedUrlName(string $wantedUrlName)
    {
        $this->wantedUrlName = $wantedUrlName;
    }
}
