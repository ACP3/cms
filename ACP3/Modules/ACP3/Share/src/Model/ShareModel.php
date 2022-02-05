<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor\ColumnType\BooleanColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\RawColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\SerializableColumnType;
use ACP3\Modules\ACP3\Share\Installer\Schema;

class ShareModel extends AbstractModel
{
    public const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * {@inheritdoc}
     */
    public function save(array $rawData, ?int $entryId = null): int
    {
        $rawData = array_merge($rawData, $this->mapDataFromRequest($rawData));

        return parent::save($rawData, $entryId);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
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
    protected function getAllowedColumns(): array
    {
        return [
            'uri' => RawColumnType::class,
            'active' => BooleanColumnType::class,
            'services' => SerializableColumnType::class,
            'ratings_active' => BooleanColumnType::class,
        ];
    }
}
