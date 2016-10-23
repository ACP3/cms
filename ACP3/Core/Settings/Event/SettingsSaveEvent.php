<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Settings\Event;


use Symfony\Component\EventDispatcher\Event;

class SettingsSaveEvent extends Event
{
    /**
     * @var string
     */
    private $module;
    /**
     * @var array
     */
    private $data;

    public function __construct($module, array $data)
    {
        $this->data = $data;
        $this->module = $module;
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
