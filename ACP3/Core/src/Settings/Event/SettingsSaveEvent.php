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
     * @var array
     */
    private $data;

    public function __construct(private string $module, array $data)
    {
        $this->data = $data;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function addData(string $key, $values): void
    {
        if (!\array_key_exists($key, $this->data)) {
            $this->data[$key] = $values;
        }
    }
}
