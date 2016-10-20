<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Model;


use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Modules\ACP3\Files\Installer\Schema;

class FilesModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @param array $data
     * @param int $userId
     * @param int|null $entryId
     * @return bool|int
     */
    public function saveFile(array $data, $userId, $entryId = null)
    {
        $data = array_merge($data, [
            'category_id' => (int)$data['cat'],
            'user_id' => $userId,
        ]);

        if (!empty($data['filesize'])) {
            $data['size'] = $data['filesize'];
        }

        return $this->save($data, $entryId);
    }

    /**
     * @return array
     */
    protected function getAllowedColumns()
    {
        return [
            'start' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'end' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'category_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'title' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'text' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT_WYSIWYG,
            'comments' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'user_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'file' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW,
            'size' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW
        ];
    }
}
