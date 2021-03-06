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
     * @var array
     */
    private $allowedColumns;
    /**
     * @var array
     */
    private $rawData;
    /**
     * @var array|null
     */
    private $currentData;

    public function __construct(array $rawData, ?array $currentData, array $allowedColumns)
    {
        $this->rawData = $rawData;
        $this->allowedColumns = $allowedColumns;
        $this->currentData = $currentData;
    }

    public function getRawData(): array
    {
        return $this->rawData;
    }

    /**
     * @param mixed $value
     */
    public function addRawData(string $key, $value): void
    {
        if (!\array_key_exists($key, $this->rawData)) {
            $this->rawData[$key] = $value;
        }
    }

    /**
     * @param mixed $value
     */
    public function replaceRawData(string $key, $value): void
    {
        if (\array_key_exists($key, $this->getAllowedColumns())) {
            $this->rawData[$key] = $value;
        }
    }

    public function getCurrentData(): ?array
    {
        return $this->currentData;
    }

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
