<?php

namespace ACP3\Modules\ACP3\Emoticons;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\ACP3\Emoticons
 */
class Model extends Core\Model
{
    const TABLE_NAME = 'emoticons';

    /**
     * @param $id
     *
     * @return bool
     */
    public function resultExists($id)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = ?', [$id]) > 0;
    }

    /**
     * @param $moduleId
     *
     * @return bool
     */
    public function resultsExist($moduleId)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE module_id = ?', [$moduleId]) > 0;
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = ?', [$id]);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getOneImageById($id)
    {
        return $this->db->fetchColumn('SELECT img FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = ?', [$id]);
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' ORDER BY id DESC');
    }
}
