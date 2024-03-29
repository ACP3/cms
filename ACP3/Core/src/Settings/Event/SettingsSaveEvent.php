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
     * @param array<string, mixed> $data
     */
    public function __construct(private readonly string $module, private array $data)
    {
    }

    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function addData(string $key, mixed $values): void
    {
        if (!\array_key_exists($key, $this->data)) {
            $this->data[$key] = $values;
        }
    }
}
