<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Model\Repository;

use ACP3\Core;

class SeoRepository extends Core\Model\Repository\AbstractRepository
{
    const TABLE_NAME = 'seo';

    /**
     * @param string $path
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function uriAliasExists(string $path)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE `uri` = ?', [$path]) > 0;
    }

    /**
     * @param string $alias
     * @param string $path
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function uriAliasExistsByAlias(string $alias, string $path = '')
    {
        return $this->db->fetchColumn(
                'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE `alias` = ? AND `uri` != ?',
            [$alias, $path]
        ) > 0;
    }

    /**
     * @param string $uri
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getOneByUri(string $uri)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->getTableName() . ' WHERE `uri` = ?', [$uri]);
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllMetaTags()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->getTableName() . ' WHERE `alias` != "" OR `keywords` != "" OR `description` != "" OR `robots` != 0');
    }

    /**
     * @param string $alias
     *
     * @return bool|string
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getUriByAlias(string $alias)
    {
        return $this->db->fetchColumn('SELECT `uri` FROM ' . $this->getTableName() . ' WHERE `alias` = ?', [$alias]);
    }
}
