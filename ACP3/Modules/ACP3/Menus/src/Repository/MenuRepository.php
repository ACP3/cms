<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Repository;

use ACP3\Core;

class MenuRepository extends Core\Repository\AbstractRepository
{
    public const TABLE_NAME = 'menus';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function menuExists(int $menuId): bool
    {
        return (int) $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()} WHERE id = :id", ['id' => $menuId]) > 0;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function menuExistsByName(string $menuName, int $menuId = null): bool
    {
        $where = !empty($menuId) ? ' AND id != :id' : '';

        return (int) $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()} WHERE index_name = :indexName" . $where, ['indexName' => $menuName, 'id' => $menuId]) > 0;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getMenuNameById(int $menuId): bool|string
    {
        return $this->db->fetchColumn(
            "SELECT `index_name` FROM {$this->getTableName()} WHERE id = ?",
            [$menuId]
        );
    }

    /**
     * @return array<array<string, mixed>>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAllMenus(int $limitStart = null, int $resultsPerPage = null): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()} ORDER BY title ASC, id ASC" .
            $this->buildLimitStmt($limitStart, $resultsPerPage)
        );
    }
}
