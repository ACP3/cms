<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Application\Event;

use Symfony\Component\EventDispatcher\Event;

class ControllerActionBeforeDispatchEvent extends Event
{
    /**
     * @var string
     */
    private $controllerServiceId;
    /**
     * @var array
     */
    private $serviceIdParts = [];

    /**
     * FrontControllerDispatchEvent constructor.
     *
     * @param string $controllerServiceId
     */
    public function __construct($controllerServiceId)
    {
        $this->controllerServiceId = $controllerServiceId;

        $this->splitServiceIdIntoParts();
    }

    protected function splitServiceIdIntoParts()
    {
        $this->serviceIdParts = \explode('.', $this->controllerServiceId);
    }

    /**
     * @return string
     */
    public function getControllerServiceId(): string
    {
        return $this->controllerServiceId;
    }

    /**
     * @return string
     */
    public function getControllerArea(): string
    {
        return $this->serviceIdParts[2] ?? '';
    }

    /**
     * @return string
     */
    public function getControllerModule(): string
    {
        return $this->serviceIdParts[0] ?? '';
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->serviceIdParts[3] ?? '';
    }

    /**
     * @return string
     */
    public function getControllerAction(): string
    {
        return $this->serviceIdParts[4] ?? '';
    }
}
