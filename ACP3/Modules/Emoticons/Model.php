<?php

namespace ACP3\Modules\Emoticons;

use ACP3\Core;

/**
 * Description of Model
 *
 * @author goratsch
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'emoticons';

    public function resultExists($id)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = ?', array($id)) > 0 ? true : false;
    }

    public function resultsExist($moduleId)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE module_id = ?', array($moduleId)) > 0 ? true : false;
    }

    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = ?', array($id));
    }

    public function getOneImageById($id)
    {
        return $this->db->fetchColumn('SELECT img FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = ?', array($id));
    }

    public function getAll()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' ORDER BY id DESC');
    }

}
