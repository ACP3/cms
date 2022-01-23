<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ModelSavePrepareDataEvent extends Event
{
    /**
     * @param array<string, mixed>      $rawData
     * @param array<string, mixed>|null $currentData
     * @param array<string, string>     $allowedColumns
     */
    public function __construct(private array $rawData, private ?array $currentData, private array $allowedColumns)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function getRawData(): array
    {
        return $this->rawData;
    }

    public function addRawData(string $key, mixed $value): void
    {
        if (!\array_key_exists($key, $this->rawData)) {
            $this->rawData[$key] = $value;
        }
    }

    public function replaceRawData(string $key, mixed $value): void
    {
        if (\array_key_exists($key, $this->getAllowedColumns())) {
            $this->rawData[$key] = $value;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getCurrentData(): ?array
    {
        return $this->currentData;
    }

    /**
     * @return array<string, string>
     */
    public function getAllowedColumns(): array
    {
        return $this->allowedColumns;
    }

    public function addAllowedColumn(string $key, string $dataType): void
    {
        if (!\array_key_exists($key, $this->allowedColumns)) {
            $this->allowedColumns[$key] = $dataType;
        }
    }
}
