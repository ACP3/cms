<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Model;


use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Modules\ACP3\Seo\Installer\Schema;

class SeoModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @param array $data
     * @param int|null $entryId
     * @return bool|int
     */
    public function saveUriAlias(array $data, $entryId = null)
    {
        $data = array_merge($data, [
            'keywords' => $data['seo_keywords'],
            'description' => $data['seo_description'],
            'robots' => $data['seo_robots']
        ]);

        return $this->save($data, $entryId);
    }

    /**
     * @return array
     */
    protected function getAllowedColumns()
    {
        return [
            'uri' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW,
            'alias' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW,
            'keywords' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'description' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'robots' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT
        ];
    }
}
