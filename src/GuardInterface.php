<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 5/20/2016
 * Time: 8:39 PM
 */

namespace Dot\Rbac\Guard;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface GuardInterface
 * @package Dot\Rbac\Guard
 */
interface GuardInterface
{
    const POLICY_DENY = 'deny';
    const POLICY_ALLOW = 'allow';

    const CONDITION_OR = 'OR';
    const CONDITION_AND = 'AND';

    /**
     * Checks if the user is authorized to get through the guard
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return bool
     */
    public function isGranted(ServerRequestInterface $request, ResponseInterface $response);

    /**
     * @return int
     */
    public function getPriority();
}