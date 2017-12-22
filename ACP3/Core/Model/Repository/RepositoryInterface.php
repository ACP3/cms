<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model\Repository;

/**
 * Interface RepositoryInterface
 * @package ACP3\Core\Model\Repository
 */
interface RepositoryInterface
{
    /**
     * Executes the SQL insert statement
     *
     * @param array $data
     * @return bool|int
     */
    public function insert(array $data);

    /**
     * @param string $tableName
     *
     * @return string
     */
    public function getTableName($tableName = '');

    /**
     * Executes the SQL delete statement
     *
     * @param int|array $entryId
     * @param string $columnName
     * @return bool|int
     */
    public function delete($entryId, $columnName = 'id');

    /**
     * Executes the SQL update statement
     *
     * @param array $data
     * @param int|array $entryId
     * @return bool|int
     */
    public function update(array $data, $entryId);

    /**
     * @param int $entryId
     *
     * @return array
     */
    public function getOneById($entryId);
}
