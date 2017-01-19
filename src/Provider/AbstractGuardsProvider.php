<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/21/2016
 * Time: 12:45 AM
 */

namespace Dot\Rbac\Guard\Provider;

use Dot\Rbac\Guard\GuardPluginManager;

/**
 * Class AbstractGuardsProvider
 * @package Dot\Rbac\Guard\Provider
 */
abstract class AbstractGuardsProvider implements GuardsProviderInterface
{
    /** @var  GuardPluginManager */
    protected $guardManager;

    /**
     * AbstractGuardsProvider constructor.
     * @param GuardPluginManager $manager
     */
    public function __construct(GuardPluginManager $manager)
    {
        $this->guardManager = $manager;
    }

    /**
     * @return GuardPluginManager
     */
    public function getGuardManager()
    {
        return $this->guardManager;
    }

    /**
     * @param GuardPluginManager $guardManager
     * @return AbstractGuardsProvider
     */
    public function setGuardManager($guardManager)
    {
        $this->guardManager = $guardManager;
        return $this;
    }
}
