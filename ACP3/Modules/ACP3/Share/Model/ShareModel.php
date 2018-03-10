<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Modules\ACP3\Seo\Installer\Schema;

class ShareModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * {@inheritdoc}
     */
    public function save(array $data, $entryId = null)
    {
        $data = \array_merge($data, [
            'title' => $data['share_title'],
            'description' => $data['share_description'],
        ]);

        return parent::save($data, $entryId);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAllowedColumns()
    {
        return [
            'uri' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW,
            'description' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
        ];
    }
}
