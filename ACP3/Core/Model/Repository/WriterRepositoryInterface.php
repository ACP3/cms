<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\Repository;

interface WriterRepositoryInterface
{
    /**
     * Executes the SQL insert statement.
     *
     * @param array $data
     *
     * @return int
     */
    public function insert(array $data);

    /**
     * Executes the SQL delete statement.
     *
     * @param int|array $entryId
     * @param string    $columnName
     *
     * @return int
     */
    public function delete($entryId, string $columnName = 'id');

    /**
     * Executes the SQL update statement.
     *
     * @param array     $data
     * @param int|array $entryId
     *
     * @return int
     */
    public function update(array $data, $entryId);
}
