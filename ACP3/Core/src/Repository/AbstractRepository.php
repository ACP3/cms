<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Repository;

use ACP3\Core\Database\Connection;
use Doctrine\DBAL\Exception;

abstract class AbstractRepository implements RepositoryInterface
{
    public const TABLE_NAME = '';
    public const PRIMARY_KEY_COLUMN = 'id';

    public function __construct(protected Connection $db)
    {
    }

    /**
     * @throws Exception
     */
    public function insert(array $data): ?int
    {
        $affectedRows = $this->db->getConnection()->insert(
            $this->getTableName(),
            $data
        );

        try {
            return (int) $this->db->getConnection()->lastInsertId();
        } catch (Exception $exception) {
            // Handle the case where some tables don't have an actual identity colum
            if ($affectedRows > 0) {
                return null;
            }

            throw $exception;
        }
    }

    public function getTableName(string $tableName = ''): string
    {
        return $this->db->getPrefixedTableName(!empty($tableName) ? $tableName : static::TABLE_NAME);
    }

    /**
     * @throws Exception
     */
    public function delete(array|int|string $entryId, ?string $columnName = null): int
    {
        return $this->db->getConnection()->delete(
            $this->getTableName(),
            $this->getIdentifier($entryId, $columnName)
        );
    }

    /**
     * @param array<string, int|string>|int|string $entryId
     *
     * @return array<string, int|string>
     */
    private function getIdentifier(array|int|string $entryId, ?string $columnName = null): array
    {
        return \is_array($entryId) === true ? $entryId : [$columnName ?? static::PRIMARY_KEY_COLUMN => (int) $entryId];
    }

    /**
     * @throws Exception
     */
    public function update(array $data, array|int|string $entryId): int
    {
        return $this->db->getConnection()->update(
            $this->getTableName(),
            $data,
            $this->getIdentifier($entryId)
        );
    }

    /**
     * Build the SQL limit.
     */
    protected function buildLimitStmt(?int $limitStart = null, ?int $resultsPerPage = null): string
    {
        if ($limitStart !== null && $resultsPerPage !== null) {
            return " LIMIT {$limitStart},{$resultsPerPage}";
        }

        if ($limitStart !== null) {
            return " LIMIT {$limitStart}";
        }

        return '';
    }

    /**
     * @throws Exception
     */
    public function getOneById(int|string $entryId): array
    {
        return $this->db->fetchAssoc("SELECT * FROM {$this->getTableName()} WHERE id = ?", [$entryId]);
    }
}
