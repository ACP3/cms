<?php

namespace ACP3\Modules\Categories;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\Categories
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'categories';

    public function resultExists($id)
    {
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = ?', array($id)) > 0 ? true : false;
    }

    public function resultIsDuplicate($title, $module, $categoryId)
    {
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' AS c JOIN ' . $this->prefix . \ACP3\Modules\System\Model::TABLE_NAME . ' AS m ON(m.id = c.module_id) WHERE c.title = ? AND m.name = ? AND c.id != ?', array($title, $module, $categoryId)) > 0 ? true : false;
    }

    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = ?', array($id));
    }

    public function getTitleById($id)
    {
        return $this->db->fetchColumn('SELECT title FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = ?', array($id));
    }

    public function getAllByModuleName($moduleName)
    {
        return $this->db->fetchAll('SELECT c.* FROM ' . $this->prefix . static::TABLE_NAME . ' AS c JOIN ' . $this->prefix . \ACP3\Modules\System\Model::TABLE_NAME . ' AS m ON(m.id = c.module_id) WHERE m.name = ? ORDER BY c.title ASC', array($moduleName));
    }

    public function getAllWithModuleName()
    {
        return $this->db->fetchAll('SELECT c.*, m.name AS module FROM ' . $this->prefix . static::TABLE_NAME . ' AS c JOIN ' . $this->prefix . \ACP3\Modules\System\Model::TABLE_NAME . ' AS m ON(m.id = c.module_id) ORDER BY m.name ASC, c.title DESC, c.id DESC');
    }

    public function getModuleNameFromCategoryId($categoryId)
    {
        return $this->db->fetchColumn('SELECT m.name FROM ' . $this->prefix . \ACP3\Modules\System\Model::TABLE_NAME . ' AS m JOIN ' . $this->prefix . static::TABLE_NAME . ' AS c ON(m.id = c.module_id) WHERE c.id = ?', array($categoryId));
    }

    public function getCategoryDeleteInfosById($id)
    {
        return $this->db->fetchAssoc('SELECT c.picture, m.name AS module FROM ' . $this->prefix . static::TABLE_NAME . ' AS c JOIN ' . $this->prefix . \ACP3\Modules\System\Model::TABLE_NAME . ' AS m ON(m.id = c.module_id) WHERE c.id = ?', array($id));
    }

}
