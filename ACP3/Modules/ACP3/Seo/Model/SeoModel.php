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
     * @inheritdoc
     */
    public function save(array $data, $entryId = null)
    {
        $data = \array_merge($data, [
            'title' => $data['seo_title'],
            'keywords' => $data['seo_keywords'],
            'description' => $data['seo_description'],
            'robots' => $data['seo_robots'],
        ]);

        return parent::save($data, $entryId);
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
