<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Model\Repository;

use ACP3\Core;

/**
 * Class SeoRepository
 * @package ACP3\Modules\ACP3\Seo\Model\Repository
 */
class SeoRepository extends Core\Model\AbstractRepository
{
    const TABLE_NAME = 'seo';

    /**
     * @param string $path
     *
     * @return bool
     */
    public function uriAliasExists($path)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE `uri` = ?', [$path]) > 0;
    }

    /**
     * @param string $alias
     * @param string $path
     *
     * @return bool
     */
    public function uriAliasExistsByAlias($alias, $path = '')
    {
        return $this->db->fetchColumn(
            'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE `alias` = ? AND `uri` != ?',
            [$alias, $path]
        ) > 0;
    }

    /**
     * @param int $seoId
     *
     * @return array
     */
    public function getOneById($seoId)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->getTableName() . ' WHERE `id` = ?', [(int)$seoId]);
    }

    /**
     * @return array
     */
    public function getAllMetaTags()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->getTableName() . ' WHERE `alias` != "" OR `keywords` != "" OR `description` != "" OR `robots` != 0');
    }

    /**
     * @param string $alias
     *
     * @return bool|string
     */
    public function getUriByAlias($alias)
    {
        return $this->db->fetchColumn('SELECT `uri` FROM ' . $this->getTableName() . ' WHERE `alias` = ?', [$alias]);
    }
}
