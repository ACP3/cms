<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Settings\Event;

use Symfony\Contracts\EventDispatcher\Event;

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

    public function __construct(string $module, array $data)
    {
        $this->data = $data;
        $this->module = $module;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
