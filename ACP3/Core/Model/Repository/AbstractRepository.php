<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model\Repository;

use ACP3\Core\Database\Connection;

/**
 * Class AbstractRepository
 * @package ACP3\Core\Model\Repository
 */
abstract class AbstractRepository
{
    const TABLE_NAME = '';

    /**
     * @var \ACP3\Core\Database\Connection
     */
    protected $db;

    /**
     * @param \ACP3\Core\Database\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Executes the SQL insert statement
     *
     * @param array $data
     * @return bool|int
     */
    public function insert(array $data)
    {
        return $this->db->executeTransactionalQuery(function () use ($data) {
            $this->db->getConnection()->insert(
                $this->getTableName(),
                $data
            );
            return (int)$this->db->getConnection()->lastInsertId();
        });
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
     * Executes the SQL delete statement
     *
     * @param int|array $entryId
     * @param string $columnName
     * @return bool|int
     */
    public function delete($entryId, $columnName = 'id')
    {
        return $this->db->executeTransactionalQuery(function () use ($entryId, $columnName) {
            return $this->db->getConnection()->delete(
                $this->getTableName(),
                $this->getIdentifier($entryId, $columnName)
            );
        });
    }

    /**
     * @param int|array $entryId
     * @param string    $columnName
     *
     * @return array
     */
    private function getIdentifier($entryId, $columnName = 'id')
    {
        return is_array($entryId) === true ? $entryId : [$columnName => (int)$entryId];
    }

    /**
     * Executes the SQL update statement
     *
     * @param array $data
     * @param int|array $entryId
     * @return bool|int
     */
    public function update(array $data, $entryId)
    {
        return $this->db->executeTransactionalQuery(function () use ($data, $entryId) {
            return $this->db->getConnection()->update(
                $this->getTableName(),
                $data,
                $this->getIdentifier($entryId)
            );
        });
    }

    /**
     * Build the SQL limit
     *
     * @param int|string $limitStart
     * @param int|string $resultsPerPage
     *
     * @return string
     */
    protected function buildLimitStmt($limitStart = '', $resultsPerPage = '')
    {
        if ($limitStart !== '' && $resultsPerPage !== '') {
            return ' LIMIT ' . ((int)$limitStart) . ',' . ((int)$resultsPerPage);
        } elseif ($limitStart !== '') {
            return ' LIMIT ' . ((int)$limitStart);
        }

        return '';
    }

    /**
     * @param int $entryId
     *
     * @return array
     */
    public function getOneById($entryId)
    {
        return $this->db->fetchAssoc("SELECT * FROM {$this->getTableName()} WHERE id = ?", [$entryId]);
    }
}
