<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Model\Repository;

use ACP3\Core;
use ACP3\Core\NestedSet\Model\Repository\BlockAwareNestedSetRepositoryInterface;
use ACP3\Modules\ACP3\System\Model\Repository\ModulesRepository;

class CategoryRepository extends Core\NestedSet\Model\Repository\NestedSetRepository implements BlockAwareNestedSetRepositoryInterface
{
    const TABLE_NAME = 'categories';
    public const BLOCK_COLUMN_NAME = 'module_id';

    /**
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resultExists(int $categoryId)
    {
        return (int) $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()} WHERE id = ?", [$categoryId]) > 0;
    }

    /**
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resultIsDuplicate(string $title, int $moduleId, int $categoryId)
    {
        return (int) $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()} WHERE title = ? AND module_id = ? AND id != ?", [$title, $moduleId, $categoryId]) > 0;
    }

    /**
     * @param int $categoryId
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getOneById($categoryId)
    {
        return $this->db->fetchAssoc("SELECT * FROM {$this->getTableName()} WHERE id = ?", [$categoryId]);
    }

    /**
     * @return string
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getTitleById(int $categoryId)
    {
        return $this->db->fetchColumn("SELECT `title` FROM {$this->getTableName()} WHERE id = ?", [$categoryId]);
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllByModuleName(string $moduleName)
    {
        return $this->db->fetchAll(
            'SELECT c.*, COUNT(*)-1 AS `level`, ROUND((c.right_id - c.left_id - 1) / 2) AS children FROM ' . $this->getTableName() . ' AS main, ' . $this->getTableName() . ' AS c JOIN ' . $this->getTableName(ModulesRepository::TABLE_NAME) . ' AS m ON(m.id = c.module_id) WHERE m.name = ? AND c.left_id BETWEEN main.left_id AND main.right_id GROUP BY c.left_id, c.right_id, c.root_id, c.parent_id, c.id, c.title, c.picture, c.description, c.module_id ORDER BY c.left_id ASC',
            [$moduleName]
        );
    }

    /**
     * @return string
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getModuleNameFromCategoryId(int $categoryId)
    {
        return $this->db->fetchColumn(
            'SELECT m.name FROM ' . $this->getTableName(ModulesRepository::TABLE_NAME) . ' AS m JOIN ' . $this->getTableName() . ' AS c ON(m.id = c.module_id) WHERE c.id = ?',
            [$categoryId]
        );
    }

    /**
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getModuleIdByCategoryId(int $categoryId)
    {
        return (int) $this->db->fetchColumn("SELECT `module_id` FROM {$this->getTableName()} WHERE `id` = ?", [$categoryId]);
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getCategoryDeleteInfosById(int $categoryId)
    {
        return $this->db->fetchAssoc(
            'SELECT c.picture, m.name AS module FROM ' . $this->getTableName() . ' AS c JOIN ' . $this->getTableName(ModulesRepository::TABLE_NAME) . ' AS m ON(m.id = c.module_id) WHERE c.id = ?',
            [$categoryId]
        );
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getOneByTitleAndModule(string $title, string $moduleName)
    {
        return $this->db->fetchAssoc(
            'SELECT c.* FROM ' . $this->getTableName() . ' AS c JOIN ' . $this->getTableName(ModulesRepository::TABLE_NAME) . ' AS m ON(m.id = c.module_id) WHERE c.title = ? AND m.name = ?',
            [$title, $moduleName]
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fetchAllSortedByBlock()
    {
        return $this->db->fetchAll("SELECT * FROM {$this->getTableName()} ORDER BY `module_id` ASC");
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllSiblingsAsId(int $categoryId)
    {
        $categoryIds = [];
        foreach ($this->fetchNodeWithSiblings($categoryId) as $category) {
            $categoryIds[] = $category['id'];
        }

        return $categoryIds;
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllDirectSiblings(int $categoryId)
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()} WHERE `parent_id` = ?",
            [$categoryId]
        );
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllRootCategoriesByModuleId(int $moduleId)
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()} WHERE `module_id` = ? AND `parent_id` = ?",
            [$moduleId, 0]
        );
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllRootCategoriesByModuleName(string $moduleName)
    {
        return $this->db->fetchAll(
            "SELECT c.* FROM {$this->getTableName()} AS c JOIN {$this->getTableName(ModulesRepository::TABLE_NAME)} AS m ON(m.id = c.module_id) WHERE m.`name` = ? AND c.`parent_id` = ?",
            [$moduleName, 0]
        );
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllByModuleId(int $moduleId)
    {
        return $this->db->fetchAll(
            'SELECT c.*, COUNT(*)-1 AS `level`, ROUND((c.right_id - c.left_id - 1) / 2) AS children FROM ' . $this->getTableName() . ' AS main, ' . $this->getTableName() . ' AS c WHERE c.module_id = ? AND c.left_id BETWEEN main.left_id AND main.right_id GROUP BY c.left_id, c.id, c.root_id, c.parent_id, c.right_id, c.title, c.picture, c.description, c.module_id ORDER BY c.left_id ASC',
            [$moduleId]
        );
    }
}
