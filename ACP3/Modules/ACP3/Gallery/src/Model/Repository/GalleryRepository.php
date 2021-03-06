<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Model\Repository;

use ACP3\Core\Model\Repository\AbstractRepository;
use ACP3\Core\Model\Repository\PublicationPeriodAwareTrait;

class GalleryRepository extends AbstractRepository
{
    use PublicationPeriodAwareTrait;

    public const TABLE_NAME = 'gallery';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function galleryExists(int $galleryId, string $time = ''): bool
    {
        $period = !empty($time) ? ' AND `active` = :active AND ' . $this->getPublicationPeriod() : '';

        return (int) $this->db->fetchColumn(
            'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = :id' . $period,
                ['id' => $galleryId, 'active' => 1, 'time' => $time]
        ) > 0;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getGalleryTitle(int $galleryId): string
    {
        return $this->db->fetchColumn(
            'SELECT title FROM ' . $this->getTableName() . ' WHERE id = ?',
            [$galleryId]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function countAll(string $time): int
    {
        $where = !empty($time) ? ' WHERE `active` = :active AND ' . $this->getPublicationPeriod() : '';

        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()}{$where}",
            ['active' => 1, 'time' => $time]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAll(string $time = '', ?int $limitStart = null, ?int $resultsPerPage = null): array
    {
        $where = !empty($time) ? ' WHERE `active` = :active AND ' . $this->getPublicationPeriod('g.') : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);

        return $this->db->fetchAll(
            "
                SELECT g.id, g.active, g.start, g.end, g.updated_at, g.title, g.description, g.user_id,
                       COUNT(p.gallery_id) AS pics,
                       (SELECT fp.`file` FROM {$this->getTableName(PictureRepository::TABLE_NAME)} AS fp WHERE fp.gallery_id = g.id ORDER BY fp.pic ASC LIMIT 1) AS file
                       FROM {$this->getTableName()} AS g
                  LEFT JOIN {$this->getTableName(PictureRepository::TABLE_NAME)} AS p ON(g.id = p.gallery_id)
                   {$where}
                   GROUP BY g.id, g.active, g.start, g.end, g.updated_at, g.title, g.description, g.user_id
                   ORDER BY g.start DESC,
                            g.end DESC,
                            g.id DESC
                   {$limitStmt};",
            ['active' => 1, 'time' => $time]
        );
    }
}
