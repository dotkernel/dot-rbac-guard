<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 12:49 AM
 */

namespace Dot\Rbac\Guard\Options;

use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Dot\Rbac\Guard\GuardInterface;
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
    protected $allowRedirectParam = true;

    /** @var string */
    protected $redirectParamName = 'redirect';

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
    public function getProtectionPolicy()
    {
        return $this->protectionPolicy;
    }

    /**
     * @param string $protectionPolicy
     * @return RbacGuardOptions
     */
    public function setProtectionPolicy($protectionPolicy)
    {
        $this->protectionPolicy = $protectionPolicy;
        return $this;
    }

    /**
     * @return array
     */
    public function getGuardsProvider()
    {
        return $this->guardsProvider;
    }

    /**
     * @param array $guardsProvider
     * @return RbacGuardOptions
     */
    public function setGuardsProvider($guardsProvider)
    {
        $this->guardsProvider = $guardsProvider;
        return $this;
    }

    /**
     * @return RedirectOptions
     */
    public function getRedirectOptions()
    {
        if (!$this->redirectOptions) {
            $this->setRedirectOptions([]);
        }
        return $this->redirectOptions;
    }

    /**
     * @param RedirectOptions|array $redirectOptions
     * @return RbacGuardOptions
     */
    public function setRedirectOptions($redirectOptions)
    {
        if (is_array($redirectOptions)) {
            $this->redirectOptions = new RedirectOptions($redirectOptions);
        } elseif ($redirectOptions instanceof RedirectOptions) {
            $this->redirectOptions = $redirectOptions;
        } else {
            throw new InvalidArgumentException(sprintf(
                'RedirectOptions should be an array or an %s object. %s provided.',
                RedirectOptions::class,
                is_object($redirectOptions) ? get_class($redirectOptions) : gettype($redirectOptions)
            ));
        }

        return $this;
    }

    /**
     * @return MessagesOptions
     */
    public function getMessagesOptions()
    {
        if (!$this->messagesOptions) {
            $this->setMessagesOptions([]);
        }
        return $this->messagesOptions;
    }

    /**
     * @param MessagesOptions|array $messagesOptions
     * @return RbacGuardOptions
     */
    public function setMessagesOptions($messagesOptions)
    {
        if (is_array($messagesOptions)) {
            $this->messagesOptions = new MessagesOptions($messagesOptions);
        } elseif ($messagesOptions instanceof MessagesOptions) {
            $this->messagesOptions = $messagesOptions;
        } else {
            throw new InvalidArgumentException(sprintf(
                'MessagesOptions should be an array or an %s object. %s provided.',
                MessagesOptions::class,
                is_object($messagesOptions) ? get_class($messagesOptions) : gettype($messagesOptions)
            ));
        }
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAllowRedirectParam()
    {
        return $this->allowRedirectParam;
    }

    /**
     * @param boolean $allowRedirectParam
     * @return RbacGuardOptions
     */
    public function setAllowRedirectParam($allowRedirectParam)
    {
        $this->allowRedirectParam = $allowRedirectParam;
        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectParamName()
    {
        return $this->redirectParamName;
    }

    /**
     * @param string $redirectParamName
     * @return RbacGuardOptions
     */
    public function setRedirectParamName($redirectParamName)
    {
        $this->redirectParamName = $redirectParamName;
        return $this;
    }
}
