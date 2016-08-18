<?php
namespace ACP3\Core\Model;

use ACP3\Core\Database\Connection;

/**
 * Class AbstractRepository
 * @package ACP3\Core\Model
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
     * @param string $tableName
     *
     * @return string
     */
    protected function getTableName($tableName = '')
    {
        return $this->db->getPrefixedTableName(!empty($tableName) ? $tableName : static::TABLE_NAME);
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
}
