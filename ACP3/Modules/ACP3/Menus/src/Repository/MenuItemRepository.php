<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Repository;

use ACP3\Core\NestedSet\Repository\BlockAwareNestedSetRepositoryInterface;
use ACP3\Core\NestedSet\Repository\NestedSetRepository;
use ACP3\Modules\ACP3\Menus\Enum\PageTypeEnum;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;

class MenuItemRepository extends NestedSetRepository implements BlockAwareNestedSetRepositoryInterface
{
    public const TABLE_NAME = 'menu_items';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function menuItemExists(int $menuItemId): bool
    {
        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()} WHERE id = :id",
            ['id' => $menuItemId]
        ) > 0;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getOneMenuItemByUri(string $uri): array
    {
        if (empty($uri)) {
            return [];
        }

        return $this->db->fetchAssoc(
            "SELECT * FROM {$this->getTableName()} WHERE uri = ?",
            [$uri]
        );
    }

    /**
     * @return array<array<string, mixed>>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAllItemsByBlockId(int $menuId): array
    {
        return $this->db->fetchAll(
            "SELECT `id` FROM {$this->getTableName()} WHERE block_id = ?",
            [$menuId]
        );
    }

    /**
     * @deprecated since ACP3 version 6.6.0, to be removed with version 7.0.0.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getMenuItemUriById(int $menuItemId): string
    {
        return $this->db->fetchColumn(
            "SELECT `uri` FROM {$this->getTableName()} WHERE id = ?",
            [$menuItemId]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getMenuIdByMenuItemId(int $menuItemId): int
    {
        return (int) $this->db->fetchColumn(
            "SELECT `block_id` FROM {$this->getTableName()} WHERE id = ?",
            [$menuItemId]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getMenuItemIdByUri(string $uri): int
    {
        return (int) $this->db->fetchColumn(
            "SELECT `id` FROM {$this->getTableName()} WHERE uri = ?",
            [$uri]
        );
    }

    /**
     * @return array<array<string, mixed>>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAllMenuItems(): array
    {
        $statement = <<<SQL
SELECT n.*, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children, m.title AS block_title, m.index_name AS block_name
FROM {$this->getTableName()} AS p, {$this->getTableName()} AS n
JOIN {$this->getTableName(MenuRepository::TABLE_NAME)} AS m ON(n.block_id = m.id)
WHERE n.left_id BETWEEN p.left_id AND p.right_id
GROUP BY n.left_id, n.id, n.mode, n.block_id, n.root_id, n.parent_id, n.right_id, n.display, n.title, n.uri, n.target
ORDER BY n.left_id
SQL;

        return $this->db->fetchAll($statement);
    }

    /**
     * @return array<array<string, mixed>>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getVisibleMenuItemsByBlockName(string $blockName): array
    {
        $statement = <<<SQL
SELECT n.*, COUNT(*)-1 AS level, b.title AS block_title, b.index_name AS block_name
FROM {$this->getTableName()} AS p, {$this->getTableName()} AS n
JOIN {$this->getTableName(MenuRepository::TABLE_NAME)} AS b ON(n.block_id = b.id)
WHERE b.index_name = ?
  AND NOT EXISTS(SELECT id FROM {$this->getTableName()} AS vmi WHERE vmi.left_id <= n.left_id AND vmi.right_id >= n.right_id AND display = 0)
  AND n.left_id BETWEEN p.left_id AND p.right_id
GROUP BY n.left_id, n.id, n.mode, n.block_id, n.root_id, n.parent_id, n.right_id, n.display, n.title, n.uri, n.target, b.title, b.index_name
ORDER BY n.left_id
SQL;

        return $this->db->fetchAll($statement, [$blockName]);
    }

    /**
     * @param string[] $uris
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getLeftIdByUris(string $menuName, array $uris): int
    {
        return (int) $this->db->fetchColumn(
            "SELECT m.left_id FROM {$this->getTableName()} AS m JOIN {$this->getTableName(MenuRepository::TABLE_NAME)} AS b ON(m.block_id = b.id) WHERE b.index_name = ? AND m.uri IN(?) ORDER BY LENGTH(m.uri) DESC",
            [$menuName, array_unique($uris)],
            [ParameterType::STRING, ArrayParameterType::STRING]
        );
    }

    /**
     * @param string[] $uris
     *
     * @return array<array<string, mixed>>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getMenuItemsByUri(array $uris): array
    {
        return $this->db->fetchAll(
            "SELECT p.title, p.uri, p.left_id, p.right_id FROM {$this->getTableName()} AS c, {$this->getTableName()} AS p WHERE p.mode != :excludePageType AND c.left_id BETWEEN p.left_id AND p.right_id AND c.uri IN(:uris) GROUP BY p.uri, p.title, p.left_id, p.right_id ORDER BY p.left_id ASC",
            ['excludePageType' => PageTypeEnum::HEADLINE->value, 'uris' => array_unique($uris)],
            ['uris' => ArrayParameterType::STRING]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchAllSortedByBlock(): array
    {
        return $this->db->fetchAll("SELECT * FROM {$this->getTableName()} ORDER BY `block_id` ASC");
    }
}
