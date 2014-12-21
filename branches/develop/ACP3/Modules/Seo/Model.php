<?php
namespace ACP3\Modules\Seo;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\System
 */
class Model extends Core\Model
{
    const TABLE_NAME = 'seo';

    /**
     * @param $path
     * @return bool
     */
    public function uriAliasExists($path)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE uri = ?', [$path]) > 0;
    }

    /**
     * @param $alias
     * @param string $path
     * @return bool
     */
    public function uriAliasExistsByAlias($alias, $path = '')
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE alias = ? AND uri != ?', [$alias, $path]) > 0;
    }

    /**
     * @return array
     */
    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME);
    }

    /**
     * @param $id
     * @return array
     */
    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = ?', [(int)$id]);
    }

    /**
     * @return array
     */
    public function getAllMetaTags()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE alias != "" OR keywords != "" OR description != "" OR robots != 0');
    }
}
