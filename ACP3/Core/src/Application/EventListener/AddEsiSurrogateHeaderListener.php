<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\EventListener;

use ACP3\Core\Application\Event\ControllerActionAfterDispatchEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\HttpCache\SurrogateInterface;

class AddEsiSurrogateHeaderListener implements EventSubscriberInterface
{
    public function __construct(private SurrogateInterface $surrogate)
    {
    }

    public function __invoke(ControllerActionAfterDispatchEvent $event)
    {
        $response = $event->getResponse();

        $this->surrogate->addSurrogateControl($response);

        $event->setResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ControllerActionAfterDispatchEvent::NAME => '__invoke',
        ];
    }
}
