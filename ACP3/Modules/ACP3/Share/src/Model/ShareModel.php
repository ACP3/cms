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
    public function save(array $rawData, $entryId = null)
    {
        $rawData = \array_merge($rawData, $this->mapDataFromRequest($rawData));

        return parent::save($rawData, $entryId);
    }

    private function mapDataFromRequest(array $data): array
    {
        $mappedData = [];
        $keys = [
            'active' => 'share_active',
            'services' => 'share_services',
            'ratings_active' => 'share_ratings_active',
        ];
        foreach ($keys as $column => $formField) {
            if (isset($data[$formField])) {
                $mappedData[$column] = $data[$formField];
            }
        }

        if (isset($data['share_customize_services']) && $data['share_customize_services'] == 0) {
            $mappedData['services'] = [];
        }

        return $mappedData;
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
