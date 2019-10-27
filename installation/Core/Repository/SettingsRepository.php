<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Core\Repository;

use ACP3\Core\Model\Repository\SettingsAwareRepositoryInterface;

class SettingsRepository implements SettingsAwareRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAllSettings()
    {
        return [
            [
                'module_name' => 'system',
                'name' => 'maintenance_mode',
                'value' => 0,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function insert(array $data)
    {
        // Intentionally omitted
    }

    /**
     * {@inheritdoc}
     */
    public function getTableName($tableName = '')
    {
        // Intentionally omitted
    }

    /**
     * {@inheritdoc}
     */
    public function delete($entryId, $columnName = 'id')
    {
        // Intentionally omitted
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $data, $entryId)
    {
        // Intentionally omitted
    }

    /**
     * {@inheritdoc}
     */
    public function getOneById($entryId)
    {
        // Intentionally omitted
    }
}
