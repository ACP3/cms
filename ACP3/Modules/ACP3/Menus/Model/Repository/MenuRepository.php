<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Model\Repository;

use ACP3\Core;

/**
 * Class MenuRepository
 * @package ACP3\Modules\ACP3\Menus\Model\Repository
 */
class MenuRepository extends Core\Model\Repository\AbstractRepository
{
    const TABLE_NAME = 'menus';

    /**
     * @param int $menuId
     *
     * @return bool
     */
    public function menuExists($menuId)
    {
        return ((int)$this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()} WHERE id = :id", ['id' => $menuId]) > 0);
    }

    /**
     * @param string $menuName
     * @param int    $menuId
     *
     * @return bool
     */
    public function menuExistsByName($menuName, $menuId = 0)
    {
        $where = !empty($menuId) ? ' AND id != :id' : '';
        return ((int)$this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()} WHERE index_name = :indexName" . $where, ['indexName' => $menuName, 'id' => $menuId]) > 0);
    }

    /**
     * @param int $menuId
     *
     * @return mixed
     */
    public function getMenuNameById($menuId)
    {
        return $this->db->fetchColumn(
            "SELECT `index_name` FROM {$this->getTableName()} WHERE id = ?",
            [$menuId]
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
