<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model\Repository;

interface WriterRepositoryInterface extends TableNameAwareRepositoryInterface
{
    /**
     * Executes the SQL insert statement
     *
     * @param array $data
     * @return bool|int
     */
    public function insert(array $data);

    /**
     * Executes the SQL delete statement
     *
     * @param int|array $entryId
     * @param string $columnName
     * @return bool|int
     */
    public function delete($entryId, string $columnName = 'id');

    /**
     * Executes the SQL update statement
     *
     * @param array $data
     * @param int|array $entryId
     * @return bool|int
     */
    public function update(array $data, $entryId);
}
