<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ControllerActionBeforeDispatchEvent extends Event
{
    public const NAME = 'core.application.controller_action_dispatcher.before_dispatch';

    /**
     * @var string
     */
    private $controllerServiceId;
    /**
     * @var string[]
     */
    private $serviceIdParts = [];

    public function __construct(string $controllerServiceId)
    {
        $this->controllerServiceId = $controllerServiceId;

        $this->splitServiceIdIntoParts();
    }

    private function splitServiceIdIntoParts(): void
    {
        $this->serviceIdParts = \explode('.', $this->controllerServiceId);
    }

    public function getControllerServiceId(): string
    {
        return $this->controllerServiceId;
    }

    public function getArea(): string
    {
        return $this->serviceIdParts[2] ?? '';
    }

    public function getModule(): string
    {
        return $this->serviceIdParts[0] ?? '';
    }

    public function getController(): string
    {
        return $this->serviceIdParts[3] ?? '';
    }

    public function getControllerAction(): string
    {
        return $this->serviceIdParts[4] ?? '';
    }
}
