<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Errors\EventListener;

use ACP3\Core\Application\ControllerActionDispatcher;
use ACP3\Core\Application\Event\OutputPageExceptionEvent;
use ACP3\Core\Environment\ApplicationMode;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class HandleGenericErrorsExceptionListener implements EventSubscriberInterface
{
    /**
     * @var \ACP3\Core\Application\ControllerActionDispatcher
     */
    private $controllerActionDispatcher;
    /**
     * @var string
     */
    private $applicationMode;
    /**
     * @var string|null
     */
    private $serviceId;

    public function __construct(ControllerActionDispatcher $controllerActionDispatcher, string $applicationMode, ?string $serviceId = null)
    {
        $this->controllerActionDispatcher = $controllerActionDispatcher;
        $this->applicationMode = $applicationMode;
        $this->serviceId = $serviceId;
    }

    public function __invoke(OutputPageExceptionEvent $event): void
    {
        if ($event->hasResponse()) {
            return;
        }

        if ($this->applicationMode === ApplicationMode::DEVELOPMENT) {
            return;
        }

        $event->setResponse(
            $this->controllerActionDispatcher->dispatch($this->serviceId ?? 'errors.controller.frontend.index.server_error')
        );
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            OutputPageExceptionEvent::NAME => ['__invoke', -1024],
        ];
    }
}
