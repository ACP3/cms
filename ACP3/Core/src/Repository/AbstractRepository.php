<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Repository;

use ACP3\Core\Database\Connection;

abstract class AbstractRepository implements RepositoryInterface
{
    public const TABLE_NAME = '';
    public const PRIMARY_KEY_COLUMN = 'id';

    /**
     * @var \ACP3\Core\Database\Connection
     */
    protected $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function insert(array $data): int
    {
        $this->db->getConnection()->insert(
            $this->getTableName(),
            $data
        );

        return (int) $this->db->getConnection()->lastInsertId();
    }

    public function getTableName(string $tableName = ''): string
    {
        return $this->db->getPrefixedTableName(!empty($tableName) ? $tableName : static::TABLE_NAME);
    }

    /**
     * Executes the SQL delete statement.
     *
     * @param int|string|array $entryId
     *
     * @return bool|int
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function delete($entryId, ?string $columnName = null)
    {
        return $this->db->getConnection()->delete(
            $this->getTableName(),
            $this->getIdentifier($entryId, $columnName)
        );
    }

    /**
     * @param int|string|array $entryId
     */
    private function getIdentifier($entryId, ?string $columnName = null): array
    {
        return \is_array($entryId) === true ? $entryId : [$columnName ?? static::PRIMARY_KEY_COLUMN => (int) $entryId];
    }

    /**
     * Executes the SQL update statement.
     *
     * @param int|string|array $entryId
     *
     * @return bool|int
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function update(array $data, $entryId)
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
     * @param int|string $entryId
     *
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getOneById($entryId)
    {
        return $this->db->fetchAssoc("SELECT * FROM {$this->getTableName()} WHERE id = ?", [$entryId]);
    }
}
