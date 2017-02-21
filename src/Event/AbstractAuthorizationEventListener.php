<?php
/**
 * @copyright: DotKernel
 * @library: dot-rbac-guard
 * @author: n3vrax
 * Date: 2/1/2017
 * Time: 10:07 PM
 */

declare(strict_types = 1);

namespace Dot\Rbac\Guard\Event;

use Zend\EventManager\AbstractListenerAggregate;

/**
 * Class AbstractAuthorizationEventListener
 * @package Dot\Rbac\Guard\Event
 */
abstract class AbstractAuthorizationEventListener extends AbstractListenerAggregate implements
    AuthorizationEventListenerInterface
{
    use AuthorizationEventListenerTrait;
}
