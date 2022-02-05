<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor\ColumnType\IntegerColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\RawColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\TextColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\TextWysiwygColumnType;
use ACP3\Modules\ACP3\Seo\Installer\Schema;

class SeoModel extends AbstractModel
{
    public const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * {@inheritdoc}
     */
    public function save(array $rawData, ?int $entryId = null): int
    {
        $rawData = array_merge($rawData, [
            'title' => $rawData['seo_title'],
            'keywords' => $rawData['seo_keywords'],
            'description' => $rawData['seo_description'],
            'robots' => $rawData['seo_robots'],
            'structured_data' => $rawData['seo_structured_data'],
        ]);

        return parent::save($rawData, $entryId);
    }

    protected function getAllowedColumns(): array
    {
        return [
            'uri' => RawColumnType::class,
            'alias' => RawColumnType::class,
            'title' => TextColumnType::class,
            'keywords' => TextColumnType::class,
            'description' => TextColumnType::class,
            'robots' => IntegerColumnType::class,
            'structured_data' => TextWysiwygColumnType::class,
        ];
    }
}
