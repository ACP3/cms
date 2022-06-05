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
     * @param array<string, mixed>               $filteredData
     * @param array<string, mixed>               $rawData
     * @param int|array<string, string|int>|null $entryId
     * @param array<string, mixed>|null          $currentData
     */
    public function __construct(private readonly string $moduleName, private readonly array $filteredData, private readonly array $rawData, private readonly array|int|null $entryId, private readonly bool $isNewEntry, private readonly bool $hasDataChanges, private readonly string $tableName, private readonly ?array $currentData)
    {
    }

    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->filteredData;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRawData(): array
    {
        return $this->rawData;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getCurrentData(): ?array
    {
        return $this->currentData;
    }

    /**
     * @return int|array<string, string|int>|null
     */
    public function getEntryId(): int|array|null
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
