<?php

namespace ACP3\Modules\Users;

use ACP3\Core;

/**
 * Description of Model
 *
 * @author Tino Goratsch
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'users';

    public function __construct(\Doctrine\DBAL\Connection $db)
    {
        parent::__construct($db);
    }

    public function resultExists($id, $time = '')
    {
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = :id', array('id' => $id)) > 0 ? true : false;
    }

    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = ?', array($id));
    }

    public function countAll()
    {
        return count($this->getAll());
    }


    public function getAll($limitStart = '', $resultsPerPage = '')
    {
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' ORDER BY id DESC' . $limitStmt);
    }

    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' ORDER BY id DESC');
    }

}
