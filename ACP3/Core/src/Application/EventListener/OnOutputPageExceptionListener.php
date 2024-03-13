<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\EventListener;

use ACP3\Core\Application\Event\OutputPageExceptionEvent;
use ACP3\Core\Controller\Exception\ForwardControllerActionAwareExceptionInterface;
use ACP3\Core\Environment\ApplicationMode;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnOutputPageExceptionListener implements EventSubscriberInterface
{
    public function __construct(private readonly LoggerInterface $logger, private readonly ApplicationMode $applicationMode)
    {
    }

    public function __invoke(OutputPageExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Handle exceptions of known application-state a little different, i.e.  add them to the debug log,
        // as they are usually only relevant for development purposes.
        if ($this->applicationMode !== ApplicationMode::PRODUCTION
            && $exception instanceof ForwardControllerActionAwareExceptionInterface) {
            $this->logger->debug($exception);

            return;
        }

        $this->logger->error($exception);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OutputPageExceptionEvent::NAME => ['__invoke', 1024],
        ];
    }
}
