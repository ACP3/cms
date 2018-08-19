<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\Event\Listener;

use ACP3\Core\ACL;
use ACP3\Core\ACL\Exception\AccessForbiddenException;
use ACP3\Core\Application\Event\ControllerActionBeforeDispatchEvent;

class CheckPermissionListener
{
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;

    public function __construct(ACL $acl)
    {
        $this->acl = $acl;
    }

    /**
     * @param \ACP3\Core\Application\Event\ControllerActionBeforeDispatchEvent $event
     *
     * @throws \ACP3\Core\ACL\Exception\AccessForbiddenException
     */
    public function __invoke(ControllerActionBeforeDispatchEvent $event)
    {
        $path = $event->getArea() . '/' . $event->getModule() . '/' . $event->getController() . '/' . $event->getControllerAction();

        if ($this->acl->hasPermission($path) === false) {
            throw new AccessForbiddenException();
        }
    }
}
