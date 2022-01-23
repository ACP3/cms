<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Http\EventListener;

use ACP3\Core\Application\Event\ControllerActionRequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ResetRequestListener implements EventSubscriberInterface
{
    public function __invoke(ControllerActionRequestEvent $event): void
    {
        $request = $event->getRequest();
        $request->setPathInfo();
        $request->processQuery();
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ControllerActionRequestEvent::NAME => ['__invoke', 255],
        ];
    }
}
