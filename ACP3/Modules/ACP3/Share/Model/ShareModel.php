<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Modules\ACP3\Share\Installer\Schema;

class ShareModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * {@inheritdoc}
     */
    public function save(array $data, $entryId = null)
    {
        $data = \array_merge($data, $this->getData($data));

        return parent::save($data, $entryId);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function getData(array $data): array
    {
        $newData = [];
        $keys = [
            'active' => 'share_active',
            'services' => 'share_services',
            'ratings_active' => 'share_ratings_active',
        ];
        foreach ($keys as $column => $formField) {
            if (isset($data[$formField])) {
                $newData[$column] = $data[$formField];
            }
        }

        return $newData;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAllowedColumns()
    {
        return [
            'uri' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW,
            'active' => DataProcessor\ColumnTypes::COLUMN_TYPE_BOOLEAN,
            'services' => DataProcessor\ColumnTypes::COLUMN_TYPE_SERIALIZABLE,
            'ratings_active' => DataProcessor\ColumnTypes::COLUMN_TYPE_BOOLEAN,
        ];
    }
}
