<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Model\Repository;

use ACP3\Core;

class EmoticonRepository extends Core\Model\Repository\AbstractRepository
{
    public const TABLE_NAME = 'emoticons';

    /**
     * @param int $emoticonId
     *
     * @return bool
     */
    public function resultExists($emoticonId)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = ?', [$emoticonId]) > 0;
    }

    /**
     * @param int $moduleId
     *
     * @return bool
     */
    public function resultsExist($moduleId)
    {
        return $this->db->fetchColumn(
                'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE module_id = ?',
                [$moduleId]
        ) > 0;
    }

    /**
     * @param int $emoticonId
     *
     * @return string
     */
    public function getOneImageById($emoticonId)
    {
        return $this->db->fetchColumn('SELECT img FROM ' . $this->getTableName() . ' WHERE id = ?', [$emoticonId]);
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->getTableName() . ' ORDER BY id DESC');
    }
}
