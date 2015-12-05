<?php

namespace ACP3\Modules\ACP3\Comments\Model;

use ACP3\Core;

/**
 * Class CommentRepository
 * @package ACP3\Modules\ACP3\Comments\Model
 */
class CommentRepository extends Core\Model\AbstractRepository implements Core\Model\FloodBarrierAwareRepositoryInterface
{
    const TABLE_NAME = 'comments';

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
    public function resultsExistByModuleId($moduleId)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE module_id = ?', [$moduleId]) > 0;
    }

    /**
     * @param int $moduleId
     *
     * @return bool
     */
    public function countAll($moduleId = 0)
    {
        if ($moduleId === 0) {
            return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName());
        }

        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE module_id = ?', [$moduleId]);
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT c.*, m.name AS module FROM ' . $this->getTableName() . ' AS c JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\System\Model\ModuleRepository::TABLE_NAME) . ' AS m ON(m.id = c.module_id) WHERE c.id = ?', [$id]);
    }

    /**
     * @param $ipAddress
     *
     * @return mixed
     */
    public function getLastDateFromIp($ipAddress)
    {
        return $this->db->fetchColumn('SELECT MAX(`date`) FROM ' . $this->getTableName() . ' WHERE ip = ?', [$ipAddress]);
    }

    /**
     * @param        $moduleId
     * @param        $resultId
     * @param string $limitStart
     * @param string $resultsPerPage
     *
     * @return array
     */
    public function getAllByModule($moduleId, $resultId, $limitStart = '', $resultsPerPage = '')
    {
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT IF(c.user_id IS NULL, c.name, u.nickname) AS `name`, c.user_id, c.date, c.message FROM ' . $this->getTableName() . ' AS c LEFT JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\Users\Model\UserRepository::TABLE_NAME) . ' AS u ON (u.id = c.user_id) WHERE c.module_id = ? AND c.entry_id = ? ORDER BY c.date ASC' . $limitStmt, [$moduleId, $resultId]);
    }

    /**
     * @param $moduleId
     * @param $resultId
     *
     * @return mixed
     */
    public function countAllByModule($moduleId, $resultId)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE module_id = ? AND entry_id = ?', [$moduleId, $resultId]);
    }

    /**
     * @param $moduleId
     *
     * @return array
     */
    public function getAllByModuleInAcp($moduleId)
    {
        return $this->db->fetchAll('SELECT IF(c.user_id IS NULL, c.name, u.nickname) AS `name`, c.id, c.ip, c.user_id, c.date, c.message FROM ' . $this->getTableName() . ' AS c LEFT JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\Users\Model\UserRepository::TABLE_NAME) . ' AS u ON (u.id = c.user_id) WHERE c.module_id = ? ORDER BY c.entry_id ASC, c.date ASC', [$moduleId]);
    }

    /**
     * @return array
     */
    public function getCommentsGroupedByModule()
    {
        return $this->db->fetchAll('SELECT c.module_id, m.name AS module, COUNT(c.module_id) AS `comments_count` FROM ' . $this->getTableName() . ' AS c JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\System\Model\ModuleRepository::TABLE_NAME) . ' AS m ON(m.id = c.module_id) GROUP BY c.module_id ORDER BY m.name');
    }
}
