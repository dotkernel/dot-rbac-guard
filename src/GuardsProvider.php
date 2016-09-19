<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 5/21/2016
 * Time: 2:49 AM
 */

namespace Dot\Rbac\Guard;

/**
 * Class GuardsProvider
 * @package Dot\Rbac\Guard
 */
class GuardsProvider
{
    /** @var array  */
    protected $guards = [];

    /**
     * GuardsProvider constructor.
     * @param array $guards
     */
    public function __construct(array $guards = [])
    {
        $this->guards = $guards;
        usort($this->guards, function(GuardInterface $a, GuardInterface $b) {
            return $a->getPriority() - $b->getPriority();
        });
    }

    /**
     * @return array
     */
    public function getGuards()
    {
        return $this->guards;
    }
}