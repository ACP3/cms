<?php

namespace ACP3\Modules\ACP3\Categories;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\ACP3\Categories
 */
class Model extends Core\Model
{
    const TABLE_NAME = 'categories';

    /**
     * @param $id
     *
     * @return bool
     */
    public function resultExists($id)
    {
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = ?', [$id]) > 0;
    }

    /**
     * @param $title
     * @param $module
     * @param $categoryId
     *
     * @return bool
     */
    public function resultIsDuplicate($title, $module, $categoryId)
    {
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' AS c JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\System\Model\ModuleRepository::TABLE_NAME) . ' AS m ON(m.id = c.module_id) WHERE c.title = ? AND m.name = ? AND c.id != ?', [$title, $module, $categoryId]) > 0;
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->getTableName() . ' WHERE id = ?', [$id]);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getTitleById($id)
    {
        return $this->db->fetchColumn('SELECT title FROM ' . $this->getTableName() . ' WHERE id = ?', [$id]);
    }

    /**
     * @param $moduleName
     *
     * @return array
     */
    public function getAllByModuleName($moduleName)
    {
        return $this->db->fetchAll('SELECT c.* FROM ' . $this->getTableName() . ' AS c JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\System\Model\ModuleRepository::TABLE_NAME) . ' AS m ON(m.id = c.module_id) WHERE m.name = ? ORDER BY c.title ASC', [$moduleName]);
    }

    /**
     * @return array
     */
    public function getAllWithModuleName()
    {
        return $this->db->fetchAll('SELECT c.*, m.name AS module FROM ' . $this->getTableName() . ' AS c JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\System\Model\ModuleRepository::TABLE_NAME) . ' AS m ON(m.id = c.module_id) ORDER BY m.name ASC, c.title DESC, c.id DESC');
    }

    /**
     * @param $categoryId
     *
     * @return mixed
     */
    public function getModuleNameFromCategoryId($categoryId)
    {
        return $this->db->fetchColumn('SELECT m.name FROM ' . $this->getTableName(\ACP3\Modules\ACP3\System\Model\ModuleRepository::TABLE_NAME) . ' AS m JOIN ' . $this->getTableName() . ' AS c ON(m.id = c.module_id) WHERE c.id = ?', [$categoryId]);
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function getCategoryDeleteInfosById($id)
    {
        return $this->db->fetchAssoc('SELECT c.picture, m.name AS module FROM ' . $this->getTableName() . ' AS c JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\System\Model\ModuleRepository::TABLE_NAME) . ' AS m ON(m.id = c.module_id) WHERE c.id = ?', [$id]);
    }

    /**
     * @param $title
     * @param $module
     *
     * @return array
     */
    public function getOneByTitleAndModule($title, $module)
    {
        return $this->db->fetchAssoc('SELECT c.* FROM ' . $this->getTableName() . ' AS c JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\System\Model\ModuleRepository::TABLE_NAME) . ' AS m ON(m.id = c.module_id) WHERE c.title = ? AND m.name = ?', [$title, $module]);
    }
}
