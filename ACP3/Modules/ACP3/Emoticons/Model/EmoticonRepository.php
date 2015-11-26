<?php

namespace ACP3\Modules\ACP3\Emoticons\Model;

use ACP3\Core;

/**
 * Class EmoticonRepository
 * @package ACP3\Modules\ACP3\Emoticons\Model
 */
class EmoticonRepository extends Core\Model\AbstractRepository
{
    const TABLE_NAME = 'emoticons';

    /**
     * @param $id
     *
     * @return bool
     */
    public function resultExists($id)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = ?', [$id]) > 0;
    }

    /**
     * @param $moduleId
     *
     * @return bool
     */
    public function resultsExist($moduleId)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE module_id = ?', [$moduleId]) > 0;
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->getTableName() . ' WHERE id = ?', [$id]);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getOneImageById($id)
    {
        return $this->db->fetchColumn('SELECT img FROM ' . $this->getTableName() . ' WHERE id = ?', [$id]);
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->getTableName() . ' ORDER BY id DESC');
    }
}
