<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Model;

use ACP3\Core\Helpers\Enum\LinkTargetEnum;
use ACP3\Core\Helpers\Enum\YesNoEnum;
use ACP3\Core\Model\DataProcessor\ColumnType\IntegerColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\RawColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\TextColumnType;
use ACP3\Core\NestedSet\Model\AbstractNestedSetModel;
use ACP3\Modules\ACP3\Menus\Enum\PageTypeEnum;
use ACP3\Modules\ACP3\Menus\Installer\Schema;

class MenuItemsModel extends AbstractNestedSetModel
{
    public const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * {@inheritdoc}
     */
    public function save(array $rawData, ?int $entryId = null): int
    {
        $rawData['target'] = YesNoEnum::tryFrom($rawData['display']) === YesNoEnum::NO ? LinkTargetEnum::TARGET_SELF->value : $rawData['target'];

        if (PageTypeEnum::tryFrom($rawData['mode']) === PageTypeEnum::HEADLINE) {
            $rawData['uri'] = '';
        }

        return parent::save($rawData, $entryId);
    }

    protected function getAllowedColumns(): array
    {
        return [
            'mode' => IntegerColumnType::class,
            'block_id' => IntegerColumnType::class,
            'parent_id' => IntegerColumnType::class,
            'display' => IntegerColumnType::class,
            'title' => TextColumnType::class,
            'uri' => RawColumnType::class,
            'target' => IntegerColumnType::class,
        ];
    }
}
