<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\EventListener;

use ACP3\Core\ACL;
use ACP3\Core\ACL\Exception\AccessForbiddenException;
use ACP3\Core\Application\Event\ControllerActionBeforeDispatchEvent;
use ACP3\Core\Controller\AreaEnum;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CheckPermissionListener implements EventSubscriberInterface
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
     * @throws \ACP3\Core\ACL\Exception\AccessForbiddenException
     */
    public function __invoke(ControllerActionBeforeDispatchEvent $event)
    {
        if ($event->getArea() === AreaEnum::AREA_INSTALL) {
            return;
        }

        $path = $event->getArea() . '/' . $event->getModule() . '/' . $event->getController() . '/' . $event->getControllerAction();

        if ($this->acl->hasPermission($path) === false) {
            throw new AccessForbiddenException(\sprintf('Access forbidden for controller action "%s"', $path));
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ControllerActionBeforeDispatchEvent::NAME => '__invoke',
        ];
    }
}
