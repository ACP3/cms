<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor\ColumnType\BooleanColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\DateTimeColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\IntegerColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\TextColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\TextWysiwygColumnType;
use ACP3\Core\Model\DuplicationAwareTrait;
use ACP3\Core\Model\UpdatedAtAwareModelInterface;
use ACP3\Modules\ACP3\Articles\Installer\Schema;

class ArticlesModel extends AbstractModel implements UpdatedAtAwareModelInterface
{
    use DuplicationAwareTrait;

    public const EVENT_PREFIX = Schema::MODULE_NAME;

    public function save(array $rawData, ?int $entryId = null): int
    {
        $rawData['updated_at'] = 'now';

        return parent::save($rawData, $entryId);
    }

    /**
     * @return array<string, mixed>
     */
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
            'active' => BooleanColumnType::class,
            'start' => DateTimeColumnType::class,
            'end' => DateTimeColumnType::class,
            'updated_at' => DateTimeColumnType::class,
            'title' => TextColumnType::class,
            'subtitle' => TextColumnType::class,
            'text' => TextWysiwygColumnType::class,
            'layout' => TextColumnType::class,
            'user_id' => IntegerColumnType::class,
        ];
    }
}
