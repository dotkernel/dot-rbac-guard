<?php
/**
 * @see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
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
