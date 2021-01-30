<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller\EventListener;

use ACP3\Core\Application\ControllerActionDispatcher;
use ACP3\Core\Application\Event\OutputPageExceptionEvent;
use ACP3\Core\Controller\Exception\ForwardControllerActionAwareExceptionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ForwardControllerActionExceptionErrorListener implements EventSubscriberInterface
{
    /**
     * @var \ACP3\Core\Application\ControllerActionDispatcher
     */
    private $controllerActionDispatcher;

    public function __construct(ControllerActionDispatcher $controllerActionDispatcher)
    {
        $this->controllerActionDispatcher = $controllerActionDispatcher;
    }

    public function __invoke(OutputPageExceptionEvent $event): void
    {
        // Return early, if the event has already been handled by another exception listener
        if ($event->hasResponse()) {
            return;
        }

        $throwable = $event->getThrowable();

        if ($throwable instanceof ForwardControllerActionAwareExceptionInterface) {
            $event->setResponse(
                $this->controllerActionDispatcher->dispatch($throwable->getServiceId(), $throwable->routeParams())
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            OutputPageExceptionEvent::NAME => '__invoke',
        ];
    }
}
