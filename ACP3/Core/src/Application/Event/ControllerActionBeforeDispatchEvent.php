<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\Event;

use ACP3\Core\Controller\AreaEnum;
use Symfony\Contracts\EventDispatcher\Event;

class ControllerActionBeforeDispatchEvent extends Event
{
    /**
     * @var string[]
     */
    private array $serviceIdParts = [];

    public function __construct(private readonly string $controllerServiceId)
    {
        $this->splitServiceIdIntoParts();
    }

    private function splitServiceIdIntoParts(): void
    {
        $this->serviceIdParts = explode('.', $this->controllerServiceId);
    }

    public function getControllerServiceId(): string
    {
        return $this->controllerServiceId;
    }

    public function getArea(): AreaEnum
    {
        return AreaEnum::tryFrom($this->serviceIdParts[2] ?? '');
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
