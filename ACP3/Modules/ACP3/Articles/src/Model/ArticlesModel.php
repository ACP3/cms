<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Core\Model\DuplicationAwareTrait;
use ACP3\Core\Model\UpdatedAtAwareModelInterface;
use ACP3\Modules\ACP3\Articles\Installer\Schema;

class ArticlesModel extends AbstractModel implements UpdatedAtAwareModelInterface
{
    use DuplicationAwareTrait;

    public const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * {@inheritdoc}
     */
    public function save(array $rawData, $entryId = null): int
    {
        $rawData['updated_at'] = 'now';

        return parent::save($rawData, $entryId);
    }

    protected function getDefaultDataForDuplication(): array
    {
        return [
            'active' => 0,
            'start' => 'now',
            'end' => 'now',
        ];
    }

    protected function getAllowedColumns(): array
    {
        return [
            'active' => DataProcessor\ColumnTypes::COLUMN_TYPE_BOOLEAN,
            'start' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'end' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'updated_at' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'title' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'subtitle' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'text' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT_WYSIWYG,
            'layout' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'user_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
        ];
    }
}
