<?php

namespace ACP3\Modules\Comments;

use ACP3\Core;

/**
 * Description of Model
 *
 * @author goratsch
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'comments';

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
        return $this->db->fetchAssoc('SELECT c.*, m.name AS module FROM ' . $this->prefix . static::TABLE_NAME . ' AS c JOIN ' . $this->prefix . 'modules AS m ON(m.id = c.module_id) WHERE c.id = ?', array($id));
    }

    public function getLastDateFromIp($ipAddress)
    {
        return $this->db->fetchColumn('SELECT MAX(date) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE ip = ?', array($ipAddress));
    }

    public function getAllByModule($moduleId, $resultId, $limitStart = '', $resultsPerPage = '')
    {
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT u.nickname AS user_name, c.name, c.user_id, c.date, c.message FROM ' . $this->prefix . static::TABLE_NAME . ' AS c JOIN ' . $this->prefix . 'modules AS m ON(m.id = c.module_id) LEFT JOIN (' . $this->prefix . 'users AS u) ON u.id = c.user_id WHERE m.name = ? AND c.entry_id = ? ORDER BY c.date ASC' . $limitStmt, array($moduleId, $resultId));
    }

    public function countAllByModule($moduleId, $resultId)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' AS c JOIN ' . $this->prefix . 'modules AS m ON(m.id = c.module_id) WHERE m.name = ? AND c.entry_id = ?', array($moduleId, $resultId));
    }

    public function getAllByModuleInAcp($moduleId)
    {
        return $this->db->fetchAll('SELECT IF(c.name != "" AND c.user_id = 0,c.name,u.nickname) AS name, c.id, c.ip, c.user_id, c.date, c.message FROM ' . $this->prefix . static::TABLE_NAME . ' AS c LEFT JOIN ' . $this->prefix . 'users AS u ON u.id = c.user_id WHERE c.module_id = ? ORDER BY c.entry_id ASC, c.id ASC', array($moduleId));
    }

    public function getCommentsGroupedByModule()
    {
        return $this->db->fetchAll('SELECT c.module_id, m.name AS module, COUNT(c.module_id) AS `comments_count` FROM ' . $this->prefix . static::TABLE_NAME . ' AS c JOIN ' . $this->prefix . 'modules AS m ON(m.id = c.module_id) GROUP BY c.module_id ORDER BY m.name');
    }

}