<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\Repository;

use ACP3\Core\Database\Connection;

abstract class AbstractRepository implements WriterRepositoryInterface, ReaderRepositoryInterface, TableNameAwareRepositoryInterface
{
    const TABLE_NAME = '';
    const PRIMARY_KEY_COLUMN = 'id';

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
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function insert(array $data)
    {
        return $this->db->executeTransactionalQuery(function () use ($data) {
            $this->db->getConnection()->insert(
                $this->getTableName(),
                $data
            );

            return (int) $this->db->getConnection()->lastInsertId();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getTableName(string $tableName = ''): string
    {
        return $this->db->getPrefixedTableName(!empty($tableName) ? $tableName : static::TABLE_NAME);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function delete($entryId, string $columnName = 'id')
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
    private function getIdentifier($entryId, string $columnName = self::PRIMARY_KEY_COLUMN)
    {
        return \is_array($entryId) === true ? $entryId : [$columnName => (int) $entryId];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\DBALException
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
     * Build the SQL limit.
     *
     * @param int|string $limitStart
     * @param int|string $resultsPerPage
     *
     * @return string
     */
    protected function buildLimitStmt(?int $limitStart = null, ?int $resultsPerPage = null)
    {
        if ($limitStart !== null && $resultsPerPage !== null) {
            return ' LIMIT ' . ((int) $limitStart) . ',' . ((int) $resultsPerPage);
        } elseif ($limitStart !== null) {
            return ' LIMIT ' . ((int) $limitStart);
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getOneById(int $entryId)
    {
        return $this->db->fetchAssoc("SELECT * FROM {$this->getTableName()} WHERE id = ?", [$entryId]);
    }
}
