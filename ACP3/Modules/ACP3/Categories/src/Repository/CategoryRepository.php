<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Repository;

use ACP3\Core;
use ACP3\Core\NestedSet\Repository\BlockAwareNestedSetRepositoryInterface;
use ACP3\Modules\ACP3\System\Repository\ModulesRepository;
use Doctrine\DBAL\Exception as DBALException;

class CategoryRepository extends Core\NestedSet\Repository\NestedSetRepository implements BlockAwareNestedSetRepositoryInterface
{
    public const TABLE_NAME = 'categories';
    public const BLOCK_COLUMN_NAME = 'module_id';

    /**
     * @throws DBALException
     */
    public function resultExists(int $categoryId): bool
    {
        return (int) $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()} WHERE id = ?", [$categoryId]) > 0;
    }

    /**
     * @throws DBALException
     */
    public function resultIsDuplicate(string $title, int $moduleId, int $categoryId): bool
    {
        return (int) $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()} WHERE title = ? AND module_id = ? AND id != ?", [$title, $moduleId, $categoryId]) > 0;
    }

    /**
     * @throws DBALException
     */
    public function getOneById(int|string $entryId): array
    {
        return $this->db->fetchAssoc("SELECT * FROM {$this->getTableName()} WHERE id = ?", [$entryId]);
    }

    /**
     * @throws DBALException
     */
    public function getTitleById(int $categoryId): string
    {
        return $this->db->fetchColumn("SELECT `title` FROM {$this->getTableName()} WHERE id = ?", [$categoryId]);
    }

    /**
     * @return array<array<string, mixed>>
     *
     * @throws DBALException
     */
    public function getAllByModuleName(string $moduleName): array
    {
        return $this->db->fetchAll(
            'SELECT c.*, COUNT(*)-1 AS `level`, ROUND((c.right_id - c.left_id - 1) / 2) AS children FROM ' . $this->getTableName() . ' AS main, ' . $this->getTableName() . ' AS c JOIN ' . $this->getTableName(ModulesRepository::TABLE_NAME) . ' AS m ON(m.id = c.module_id) WHERE m.name = ? AND c.left_id BETWEEN main.left_id AND main.right_id GROUP BY c.left_id, c.right_id, c.root_id, c.parent_id, c.id, c.title, c.picture, c.description, c.module_id ORDER BY c.left_id ASC',
            [$moduleName]
        );
    }

    /**
     * @throws DBALException
     */
    public function getModuleNameFromCategoryId(int $categoryId): string
    {
        return $this->db->fetchColumn(
            'SELECT m.name FROM ' . $this->getTableName(ModulesRepository::TABLE_NAME) . ' AS m JOIN ' . $this->getTableName() . ' AS c ON(m.id = c.module_id) WHERE c.id = ?',
            [$categoryId]
        );
    }

    /**
     * @throws DBALException
     */
    public function getModuleIdByCategoryId(int $categoryId): int
    {
        return (int) $this->db->fetchColumn("SELECT `module_id` FROM {$this->getTableName()} WHERE `id` = ?", [$categoryId]);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws DBALException
     */
    public function getCategoryDeleteInfosById(int $categoryId): array
    {
        return $this->db->fetchAssoc(
            'SELECT c.picture, m.name AS module FROM ' . $this->getTableName() . ' AS c JOIN ' . $this->getTableName(ModulesRepository::TABLE_NAME) . ' AS m ON(m.id = c.module_id) WHERE c.id = ?',
            [$categoryId]
        );
    }

    /**
     * @return array<string, mixed>
     *
     * @throws DBALException
     */
    public function getOneByTitleAndModule(string $title, string $moduleName): array
    {
        return $this->db->fetchAssoc(
            'SELECT c.* FROM ' . $this->getTableName() . ' AS c JOIN ' . $this->getTableName(ModulesRepository::TABLE_NAME) . ' AS m ON(m.id = c.module_id) WHERE c.title = ? AND m.name = ?',
            [$title, $moduleName]
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws DBALException
     */
    public function fetchAllSortedByBlock(): array
    {
        return $this->db->fetchAll("SELECT * FROM {$this->getTableName()} ORDER BY `module_id` ASC");
    }

    /**
     * @return int[]
     *
     * @throws DBALException
     */
    public function getAllSiblingsAsId(int $categoryId): array
    {
        $categoryIds = [];
        foreach ($this->fetchNodeWithSiblings($categoryId) as $category) {
            $categoryIds[] = (int) $category['id'];
        }

        return $categoryIds;
    }

    /**
     * @return array<array<string, mixed>>
     *
     * @throws DBALException
     */
    public function getAllDirectSiblings(int $categoryId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()} WHERE `parent_id` = ?",
            [$categoryId]
        );
    }

    /**
     * @return array<array<string, mixed>>
     *
     * @throws DBALException
     */
    public function getAllRootCategoriesByModuleId(int $moduleId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()} WHERE `module_id` = ? AND `parent_id` = ?",
            [$moduleId, 0]
        );
    }

    /**
     * @return array<array<string, mixed>>
     *
     * @throws DBALException
     */
    public function getAllRootCategoriesByModuleName(string $moduleName): array
    {
        return $this->db->fetchAll(
            "SELECT c.* FROM {$this->getTableName()} AS c JOIN {$this->getTableName(ModulesRepository::TABLE_NAME)} AS m ON(m.id = c.module_id) WHERE m.`name` = ? AND c.`parent_id` = ?",
            [$moduleName, 0]
        );
    }

    /**
     * @return array<array<string, mixed>>
     *
     * @throws DBALException
     */
    public function getAllByModuleId(int $moduleId): array
    {
        return $this->db->fetchAll(
            'SELECT c.*, COUNT(*)-1 AS `level`, ROUND((c.right_id - c.left_id - 1) / 2) AS children FROM ' . $this->getTableName() . ' AS main, ' . $this->getTableName() . ' AS c WHERE c.module_id = ? AND c.left_id BETWEEN main.left_id AND main.right_id GROUP BY c.left_id, c.id, c.root_id, c.parent_id, c.right_id, c.title, c.picture, c.description, c.module_id ORDER BY c.left_id ASC',
            [$moduleId]
        );
    }
}
