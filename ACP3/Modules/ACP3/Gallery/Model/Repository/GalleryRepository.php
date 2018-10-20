<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Model\Repository;

use ACP3\Core;

class GalleryRepository extends Core\Model\Repository\AbstractRepository
{
    use Core\Model\Repository\PublicationPeriodAwareTrait;

    const TABLE_NAME = 'gallery';

    /**
     * @param int    $galleryId
     * @param string $time
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function galleryExists(int $galleryId, string $time = '')
    {
        $period = !empty($time) ? ' AND `active` = :active AND ' . $this->getPublicationPeriod() : '';

        return (int) $this->db->fetchColumn(
            'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = :id' . $period,
                ['id' => $galleryId, 'active' => 1, 'time' => $time]
        ) > 0;
    }

    /**
     * @param int $galleryId
     *
     * @return bool|string
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getGalleryTitle(int $galleryId)
    {
        return $this->db->fetchColumn(
            'SELECT title FROM ' . $this->getTableName() . ' WHERE id = ?',
            [$galleryId]
        );
    }

    /**
     * @param string $time
     *
     * @return bool|string
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function countAll(string $time)
    {
        $where = !empty($time) ? ' WHERE `active` = :active AND ' . $this->getPublicationPeriod() : '';

        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()}{$where}",
            ['active' => 1, 'time' => $time]
        );
    }

    /**
     * @param string   $time
     * @param int|null $limitStart
     * @param int|null $resultsPerPage
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAll(string $time = '', ?int $limitStart = null, ?int $resultsPerPage = null)
    {
        $where = !empty($time) ? ' WHERE `active` = :active AND ' . $this->getPublicationPeriod('g.') : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);

        return $this->db->fetchAll(
            "
                SELECT g.*, 
                       COUNT(p.gallery_id) AS pics,
                       (SELECT fp.`file` FROM {$this->getTableName(PictureRepository::TABLE_NAME)} AS fp WHERE fp.gallery_id = g.id ORDER BY fp.pic ASC LIMIT 1) AS file
                       FROM {$this->getTableName()} AS g
                  LEFT JOIN {$this->getTableName(PictureRepository::TABLE_NAME)} AS p ON(g.id = p.gallery_id)
                   {$where}
                   GROUP BY g.id
                   ORDER BY g.start DESC,
                            g.end DESC,
                            g.id DESC
                   {$limitStmt};",
            ['active' => 1, 'time' => $time]
        );
    }
}
