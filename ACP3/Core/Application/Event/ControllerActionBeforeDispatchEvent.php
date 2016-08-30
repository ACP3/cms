<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
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
    public function getControllerServiceId()
    {
        return $this->controllerServiceId;
    }

    /**
     * @return mixed|string
     */
    public function getControllerArea()
    {
        return isset($this->serviceIdParts[2]) ? $this->serviceIdParts[2] : '';
    }

    /**
     * @return mixed|string
     */
    public function getControllerModule()
    {
        return isset($this->serviceIdParts[3]) ? $this->serviceIdParts[3] : '';
    }

    /**
     * @return mixed|string
     */
    public function getControllerAction()
    {
        return isset($this->serviceIdParts[4]) ? $this->serviceIdParts[4] : '';
    }
}
