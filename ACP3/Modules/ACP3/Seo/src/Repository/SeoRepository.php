<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Repository;

use ACP3\Core;

class SeoRepository extends Core\Repository\AbstractRepository
{
    public const TABLE_NAME = 'seo';

    public function uriAliasExists(string $path): bool
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE `uri` = ?', [$path]) > 0;
    }

    public function uriAliasExistsByAlias(string $alias, string $path = ''): bool
    {
        return $this->db->fetchColumn(
                'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE `alias` = ? AND `uri` != ?',
                [$alias, $path]
        ) > 0;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getOneByUri(string $uri): array
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->getTableName() . ' WHERE `uri` = ?', [$uri]);
    }

    /**
     * @return array<string, mixed>[]
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAllMetaTags(): array
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->getTableName() . ' WHERE `alias` != "" OR `keywords` != "" OR `description` != "" OR `robots` != 0');
    }

    public function getUriByAlias(string $alias): ?string
    {
        return $this->db->fetchColumn('SELECT `uri` FROM ' . $this->getTableName() . ' WHERE `alias` = ?', [$alias]);
    }
}
