<?php

/**
 * see https://github.com/dotkernel/dot-rbac-guard/ for the canonical source repository
 * Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * license https://github.com/dotkernel/dot-rbac-guard/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Dot\Rbac\Guard\Event;

use Laminas\EventManager\ListenerAggregateInterface;

interface AuthorizationEventListenerInterface extends ListenerAggregateInterface
{
    public function onBeforeAuthorization(AuthorizationEvent $e);

    public function onAfterAuthorization(AuthorizationEvent $e);

    public function onForbidden(AuthorizationEvent $e);
}
