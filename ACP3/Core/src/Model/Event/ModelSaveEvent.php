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
     * @param int|array|null $entryId
     */
    public function __construct(private string $moduleName, private array $filteredData, private array $rawData, private $entryId, private bool $isNewEntry, private bool $hasDataChanges, private string $tableName, private ?array $currentData)
    {
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
