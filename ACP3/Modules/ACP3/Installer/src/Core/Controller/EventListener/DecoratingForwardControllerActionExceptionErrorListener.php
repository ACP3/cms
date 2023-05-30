<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Core\Controller\EventListener;

use ACP3\Core\Application\ControllerActionDispatcher;
use ACP3\Core\Application\Event\OutputPageExceptionEvent;
use ACP3\Core\Controller\EventListener\ForwardControllerActionExceptionErrorListener;
use ACP3\Core\Controller\Exception\ControllerActionNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DecoratingForwardControllerActionExceptionErrorListener implements EventSubscriberInterface
{
    public function __construct(private readonly ControllerActionDispatcher $controllerActionDispatcher, private readonly ForwardControllerActionExceptionErrorListener $forwardControllerActionExceptionErrorListener)
    {
    }

    public function __invoke(OutputPageExceptionEvent $event): void
    {
        if ($event->hasResponse()) {
            return;
        }

        if ($event->getThrowable() instanceof ControllerActionNotFoundException) {
            $event->setResponse(
                $this->controllerActionDispatcher->dispatch('installer.controller.installer.error.not_found')
            );
        } else {
            ($this->forwardControllerActionExceptionErrorListener)($event);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OutputPageExceptionEvent::NAME => ['__invoke', -1024],
        ];
    }
}
