<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ModelSaveEvent extends Event
{
    /**
     * @var string
     */
    private $moduleName;
    /**
     * @var array
     */
    private $filteredData;
    /**
     * @var int|array|null
     */
    private $entryId;
    /**
     * @var array
     */
    private $rawData;
    /**
     * @var bool
     */
    private $isNewEntry;
    /**
     * @var bool
     */
    private $hasDataChanges;
    /**
     * @var string
     */
    private $tableName;
    /**
     * @var array|null
     */
    private $currentData;

    /**
     * @param int|array|null $entryId
     */
    public function __construct(
        string $moduleName,
        array $filteredData,
        array $rawData,
        $entryId,
        bool $isNewEntry,
        bool $hasDataChanges,
        string $tableName,
        ?array $currentData)
    {
        $this->moduleName = $moduleName;
        $this->filteredData = $filteredData;
        $this->rawData = $rawData;
        $this->entryId = $entryId;
        $this->isNewEntry = $isNewEntry;
        $this->hasDataChanges = $hasDataChanges;
        $this->tableName = $tableName;
        $this->currentData = $currentData;
    }

    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    public function getData(): array
    {
        return $this->filteredData;
    }

    public function getRawData(): array
    {
        return $this->rawData;
    }

    public function getCurrentData(): array
    {
        return $this->currentData;
    }

    /**
     * @return int|array|null
     */
    public function getEntryId()
    {
        return $this->entryId;
    }

    public function isDeleteStatement(): bool
    {
        return \count($this->filteredData) === 0 && \is_array($this->entryId);
    }

    public function isIsNewEntry(): bool
    {
        return $this->isNewEntry;
    }

    public function hasDataChanges(): bool
    {
        return $this->hasDataChanges;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }
}
