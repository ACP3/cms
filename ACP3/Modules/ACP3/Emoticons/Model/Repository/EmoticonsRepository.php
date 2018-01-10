<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Model\Repository;

use ACP3\Core;

class EmoticonsRepository extends Core\Model\Repository\AbstractRepository
{
    const TABLE_NAME = 'emoticons';

    /**
     * @param int $emoticonId
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resultExists(int $emoticonId)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = ?', [$emoticonId]) > 0;
    }

    /**
     * @param int $moduleId
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resultsExist(int $moduleId)
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
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getOneImageById(int $emoticonId)
    {
        return $this->db->fetchColumn('SELECT img FROM ' . $this->getTableName() . ' WHERE id = ?', [$emoticonId]);
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAll()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->getTableName() . ' ORDER BY id DESC');
    }
}
