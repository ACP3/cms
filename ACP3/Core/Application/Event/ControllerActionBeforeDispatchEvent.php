<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Application\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ControllerActionBeforeDispatchEvent
 * @package ACP3\Core\Application\Event
 */
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
        $this->serviceIdParts = explode('.', $this->controllerServiceId);
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
        return isset($this->serviceIdParts[2]) ? $this->serviceIdParts[2] : '';
    }

    /**
     * @return string
     */
    public function getControllerModule(): string
    {
        return isset($this->serviceIdParts[0]) ? $this->serviceIdParts[0] : '';
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return isset($this->serviceIdParts[3]) ? $this->serviceIdParts[3] : '';
    }

    /**
     * @return string
     */
    public function getControllerAction(): string
    {
        return isset($this->serviceIdParts[4]) ? $this->serviceIdParts[4] : '';
    }
}
