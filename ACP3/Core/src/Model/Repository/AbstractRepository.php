<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\Repository;

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
     * Executes the SQL insert statement.
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function insert(array $data)
    {
        $this->db->getConnection()->insert(
            $this->getTableName(),
            $data
        );

        return (int) $this->db->getConnection()->lastInsertId();
    }

    /**
     * @param string $tableName
     *
     * @return string
     */
    public function getTableName($tableName = '')
    {
        return $this->db->getPrefixedTableName(!empty($tableName) ? $tableName : static::TABLE_NAME);
    }

    /**
     * Executes the SQL delete statement.
     *
     * @param int|array $entryId
     *
     * @return bool|int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function delete($entryId, ?string $columnName = null)
    {
        return $this->db->getConnection()->delete(
            $this->getTableName(),
            $this->getIdentifier($entryId, $columnName)
        );
    }

    /**
     * @param int|array $entryId
     */
    private function getIdentifier($entryId, ?string $columnName = null): array
    {
        return \is_array($entryId) === true ? $entryId : [$columnName ?? static::PRIMARY_KEY_COLUMN => (int) $entryId];
    }

    /**
     * Executes the SQL update statement.
     *
     * @param int|array $entryId
     *
     * @return bool|int
     *
     * @throws \Doctrine\DBAL\DBALException
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
     *
     * @return string
     */
    protected function buildLimitStmt(?int $limitStart = null, ?int $resultsPerPage = null)
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
     * @param int $entryId
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getOneById($entryId)
    {
        return $this->db->fetchAssoc("SELECT * FROM {$this->getTableName()} WHERE id = ?", [$entryId]);
    }
}
