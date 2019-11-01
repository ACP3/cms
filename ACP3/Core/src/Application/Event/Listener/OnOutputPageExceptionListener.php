<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\Event\Listener;

use ACP3\Core\Application\Event\OutputPageExceptionEvent;
use Psr\Log\LoggerInterface;

class OnOutputPageExceptionListener
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(OutputPageExceptionEvent $event): void
    {
        $this->logger->error($event->getThrowable());
    }
}
