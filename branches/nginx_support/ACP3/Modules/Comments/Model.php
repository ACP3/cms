<?php

namespace ACP3\Modules\Comments;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\Comments
 */
class Model extends Core\Model
{
    const TABLE_NAME = 'comments';

    /**
     * @param $id
     * @return bool
     */
    public function resultExists($id)
    {
        return $this->db->getConnection()->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = ?', [$id]) > 0;
    }

    /**
     * @param $moduleId
     * @return bool
     */
    public function resultsExist($moduleId)
    {
        return $this->db->getConnection()->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE module_id = ?', [$moduleId]) > 0;
    }

    /**
     * @param $id
     * @return array
     */
    public function getOneById($id)
    {
        return $this->db->getConnection()->fetchAssoc('SELECT c.*, m.name AS module FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS c JOIN ' . $this->db->getPrefix() . 'modules AS m ON(m.id = c.module_id) WHERE c.id = ?', [$id]);
    }

    /**
     * @param $ipAddress
     * @return mixed
     */
    public function getLastDateFromIp($ipAddress)
    {
        return $this->db->getConnection()->fetchColumn('SELECT MAX(date) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE ip = ?', [$ipAddress]);
    }

    /**
     * @param $moduleId
     * @param $resultId
     * @param string $limitStart
     * @param string $resultsPerPage
     * @return array
     */
    public function getAllByModule($moduleId, $resultId, $limitStart = '', $resultsPerPage = '')
    {
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->getConnection()->fetchAll('SELECT u.nickname AS user_name, c.name, c.user_id, c.date, c.message FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS c JOIN ' . $this->db->getPrefix() . 'modules AS m ON(m.id = c.module_id) LEFT JOIN (' . $this->db->getPrefix() . 'users AS u) ON u.id = c.user_id WHERE m.name = ? AND c.entry_id = ? ORDER BY c.date ASC' . $limitStmt, [$moduleId, $resultId]);
    }

    /**
     * @param $moduleId
     * @param $resultId
     * @return mixed
     */
    public function countAllByModule($moduleId, $resultId)
    {
        return $this->db->getConnection()->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS c JOIN ' . $this->db->getPrefix() . 'modules AS m ON(m.id = c.module_id) WHERE m.name = ? AND c.entry_id = ?', [$moduleId, $resultId]);
    }

    /**
     * @param $moduleId
     * @return array
     */
    public function getAllByModuleInAcp($moduleId)
    {
        return $this->db->getConnection()->fetchAll('SELECT IF(c.name != "" AND c.user_id = 0,c.name,u.nickname) AS name, c.id, c.ip, c.user_id, c.date, c.message FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS c LEFT JOIN ' . $this->db->getPrefix() . 'users AS u ON u.id = c.user_id WHERE c.module_id = ? ORDER BY c.entry_id ASC, c.id ASC', [$moduleId]);
    }

    /**
     * @return array
     */
    public function getCommentsGroupedByModule()
    {
        return $this->db->getConnection()->fetchAll('SELECT c.module_id, m.name AS module, COUNT(c.module_id) AS `comments_count` FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS c JOIN ' . $this->db->getPrefix() . 'modules AS m ON(m.id = c.module_id) GROUP BY c.module_id ORDER BY m.name');
    }
}
