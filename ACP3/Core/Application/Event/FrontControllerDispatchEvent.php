<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Application\Event;


use Symfony\Component\EventDispatcher\Event;

/**
 * Class FrontControllerDispatchEvent
 * @package ACP3\Core\Application\Event
 */
class FrontControllerDispatchEvent extends Event
{
    /**
     * @var string
     */
    private $controllerServiceId;

    /**
     * FrontControllerDispatchEvent constructor.
     *
     * @param string $controllerServiceId
     */
    public function __construct($controllerServiceId)
    {
        $this->controllerServiceId = $controllerServiceId;
    }

    /**
     * @return string
     */
    public function getControllerServiceId()
    {
        return $this->controllerServiceId;
    }
}
