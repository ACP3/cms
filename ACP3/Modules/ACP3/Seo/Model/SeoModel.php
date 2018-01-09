<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Modules\ACP3\Seo\Installer\Schema;

class SeoModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * {@inheritdoc}
     */
    public function save(array $rawData, ?int $entryId = null): int
    {
        $rawData = \array_merge($rawData, [
            'title' => $rawData['seo_title'],
            'keywords' => $rawData['seo_keywords'],
            'description' => $rawData['seo_description'],
            'robots' => $rawData['seo_robots'],
        ]);

        return parent::save($rawData, $entryId);
    }

    /**
     * @return array
     */
    protected function getAllowedColumns()
    {
        return [
            'uri' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW,
            'alias' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW,
            'title' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'keywords' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'description' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'robots' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
        ];
    }
}
