<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model;

use ACP3\Core\Repository\AbstractRepository;

trait DuplicationAwareTrait
{
    /**
     * @return int The number of the affected (duplicated) rows
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function duplicate(int $entryId): int
    {
        $resultSet = $this->getRepository()->getOneById($entryId);

        if (!empty($resultSet)) {
            $data = $this->getDataProcessor()->unescape($resultSet, $this->getAllowedColumns());

            return $this->save(array_merge($data, $this->getDefaultDataForDuplication()));
        }

        return 0;
    }

    abstract protected function getRepository(): AbstractRepository;

    abstract protected function getDataProcessor(): DataProcessor;

    /**
     * @return array<string, class-string>
     */
    abstract protected function getAllowedColumns(): array;

    /**
     * @param array<string, mixed> $rawData
     */
    abstract public function save(array $rawData, ?int $entryId = null): int;

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultDataForDuplication(): array
    {
        return [];
    }
}
