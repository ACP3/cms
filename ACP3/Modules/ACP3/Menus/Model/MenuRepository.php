<?php
namespace ACP3\Modules\ACP3\Menus\Model;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\ACP3\Menus
 */
class MenuRepository extends Core\Model
{
    const TABLE_NAME = 'menus';

    /**
     * @param int $id
     *
     * @return bool
     */
    public function menuExists($id)
    {
        return ((int)$this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()} WHERE id = :id", ['id' => $id]) > 0);
    }

    /**
     * @param string $indexName
     * @param int    $id
     *
     * @return bool
     */
    public function menuExistsByName($indexName, $id = 0)
    {
        $where = !empty($id) ? ' AND id != :id' : '';
        return ((int)$this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()} WHERE index_name = :indexName" . $where, ['indexName' => $indexName, 'id' => $id]) > 0);
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getOneById($id)
    {
        return $this->db->fetchAssoc(
            "SELECT * FROM {$this->getTableName()} WHERE id = ?",
            [$id]
        );
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function getMenuNameById($id)
    {
        return $this->db->fetchColumn(
            "SELECT `index_name` FROM {$this->getTableName()} WHERE id = ?",
            [$id]
        );
    }

    /**
     * @param string $limitStart
     * @param string $resultsPerPage
     *
     * @return array
     */
    public function getAllMenus($limitStart = '', $resultsPerPage = '')
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()} ORDER BY title ASC, id ASC" .
            $this->buildLimitStmt($limitStart, $resultsPerPage)
        );
    }
}
