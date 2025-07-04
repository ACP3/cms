<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\EventListener;

use ACP3\Core\ACL;
use ACP3\Core\ACL\Exception\AccessForbiddenException;
use ACP3\Core\Application\Event\ControllerActionBeforeDispatchEvent;
use ACP3\Core\Environment\ApplicationMode;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CheckPermissionListener implements EventSubscriberInterface
{
    public function __construct(private readonly ApplicationMode $applicationMode, private readonly ACL $acl)
    {
    }

    /**
     * @throws AccessForbiddenException
     */
    public function __invoke(ControllerActionBeforeDispatchEvent $event): void
    {
        if (\in_array($this->applicationMode, [ApplicationMode::INSTALLER, ApplicationMode::UPDATER], true)) {
            return;
        }

        $path = $event->getArea()->value . '/' . $event->getModule() . '/' . $event->getController() . '/' . $event->getControllerAction();

        if ($this->acl->hasPermission($path) === false) {
            throw new AccessForbiddenException(\sprintf('Access forbidden for controller action "%s"', $path));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ControllerActionBeforeDispatchEvent::class => '__invoke',
        ];
    }
}
