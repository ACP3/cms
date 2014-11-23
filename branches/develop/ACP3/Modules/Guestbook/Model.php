<?php

namespace ACP3\Modules\Guestbook;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\Guestbook
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'guestbook';

    /**
     * @param $id
     * @return bool
     */
    public function resultExists($id)
    {
        return ((int)$this->db->getConnection()->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = :id', array('id' => $id)) > 0);
    }

    /**
     * @param $id
     * @return array
     */
    public function getOneById($id)
    {
        return $this->db->getConnection()->fetchAssoc('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = ?', array($id));
    }

    /**
     * @param string $notify
     * @return int
     */
    public function countAll($notify = '')
    {
        return count($this->getAll($notify));
    }

    /**
     * @param string $notify
     * @param string $limitStart
     * @param string $resultsPerPage
     * @return array
     */
    public function getAll($notify = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = ($notify == 2) ? 'WHERE active = 1' : '';
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->getConnection()->fetchAll('SELECT u.id AS user_id_real, u.nickname AS user_name, u.website AS user_website, u.mail AS user_mail, g.* FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS g LEFT JOIN ' . $this->db->getPrefix() . \ACP3\Modules\Users\Model::TABLE_NAME . ' AS u ON(u.id = g.user_id) ' . $where . ' ORDER BY date DESC' . $limitStmt);
    }

    /**
     * @param $ipAddress
     * @return mixed
     */
    public function getLastDateFromIp($ipAddress)
    {
        return $this->db->getConnection()->fetchColumn('SELECT MAX(date) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE ip = ?', array($ipAddress));
    }

    /**
     * @return array
     */
    public function getAllInAcp()
    {
        return $this->db->getConnection()->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' ORDER BY date DESC, id DESC');
    }

}
