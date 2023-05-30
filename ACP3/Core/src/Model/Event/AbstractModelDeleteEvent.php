<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\Event;

abstract class AbstractModelDeleteEvent extends ModelSaveEvent
{
    /**
     * @param array<string, string|int> $entryIdList
     */
    public function __construct(private readonly string $moduleName, private readonly string $tableName, private readonly array $entryIdList)
    {
        parent::__construct($this->moduleName, [], [], $this->entryIdList, false, false, $this->tableName, null);
    }

    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return array<string, string|int>
     */
    public function getEntryIdList(): array
    {
        return $this->entryIdList;
    }

    /**
     * @deprecated since ACP3 version 6.11.0, to be removed with version 7.0.0.
     */
    public function getCurrentData(): ?array
    {
        return parent::getCurrentData();
    }

    /**
     * @deprecated since ACP3 version 6.11.0, to be removed with version 7.0.0.
     */
    public function getData(): array
    {
        return parent::getData();
    }

    /**
     * @deprecated since ACP3 version 6.11.0, to be removed with version 7.0.0.
     */
    public function getEntryId(): int|array|null
    {
        return parent::getEntryId();
    }

    /**
     * @deprecated since ACP3 version 6.11.0, to be removed with version 7.0.0.
     */
    public function getRawData(): array
    {
        return parent::getRawData();
    }

    /**
     * @deprecated since ACP3 version 6.11.0, to be removed with version 7.0.0.
     */
    public function hasDataChanges(): bool
    {
        return parent::hasDataChanges();
    }

    /**
     * @deprecated since ACP3 version 6.11.0, to be removed with version 7.0.0.
     */
    public function isDeleteStatement(): bool
    {
        return parent::isDeleteStatement();
    }

    /**
     * @deprecated since ACP3 version 6.11.0, to be removed with version 7.0.0.
     */
    public function isIsNewEntry(): bool
    {
        return parent::isIsNewEntry();
    }
}
