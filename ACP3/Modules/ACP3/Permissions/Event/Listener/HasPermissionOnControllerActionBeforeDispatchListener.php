<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Event\Listener;


use ACP3\Core\ACL;
use ACP3\Core\ACL\Exception\AccessForbiddenException;
use ACP3\Core\Application\Event\ControllerActionBeforeDispatchEvent;
use ACP3\Core\Controller\AreaEnum;

class HasPermissionOnControllerActionBeforeDispatchListener
{
    /**
     * @var ACL
     */
    private $acl;

    /**
     * HasPermissionOnControllerActionBeforeDispatchListener constructor.
     * @param ACL $acl
     */
    public function __construct(ACL $acl)
    {
        $this->acl = $acl;
    }

    /**
     * @param ControllerActionBeforeDispatchEvent $event
     * @throws AccessForbiddenException
     */
    public function hasPermission(ControllerActionBeforeDispatchEvent $event)
    {
        if ($event->getControllerArea() === AreaEnum::AREA_INSTALL) {
            return;
        }

        $path = $event->getControllerArea() . '/';
        $path .= $event->getControllerModule() . '/';
        $path .= $event->getController() . '/';
        $path .= $event->getControllerAction();

        if ($this->acl->hasPermission($path) === false) {
            throw new AccessForbiddenException();
        }
    }
}
