<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Repository;

use ACP3\Core\Repository\RepositoryInterface;

class AbstractStubRepository implements RepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function insert(array $data)
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableName($tableName = '')
    {
        return $tableName;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($entryId, ?string $columnName = null)
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $data, $entryId)
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getOneById($entryId)
    {
        return [];
    }
}
