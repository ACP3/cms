<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Repository;

use ACP3\Core;

class PictureRepository extends Core\Repository\AbstractRepository
{
    use Core\Repository\PublicationPeriodAwareTrait;

    public const TABLE_NAME = 'gallery_pictures';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function pictureExists(int $pictureId, string $time = ''): bool
    {
        $period = empty($time) === false ? ' AND `active` = :active AND ' . $this->getPublicationPeriod('g.') : '';

        return (int) $this->db->fetchColumn(
            'SELECT COUNT(*) FROM ' . $this->getTableName(GalleryRepository::TABLE_NAME) . ' AS g, ' . $this->getTableName() . ' AS p WHERE p.id = :id AND p.gallery_id = g.id' . $period,
            ['id' => $pictureId, 'active' => 1, 'time' => $time]
        ) > 0;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getOneById(int|string $entryId): array
    {
        return $this->db->fetchAssoc(
            'SELECT g.id AS gallery_id, g.title AS gallery_title, p.* FROM ' . $this->getTableName(GalleryRepository::TABLE_NAME) . ' AS g, ' . $this->getTableName() . ' AS p WHERE p.id = ? AND p.gallery_id = g.id',
            [$entryId]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getGalleryIdFromPictureId(int $pictureId): int
    {
        return (int) $this->db->fetchColumn(
            'SELECT gallery_id FROM ' . $this->getTableName() . ' WHERE id = ?',
            [$pictureId]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getLastPictureByGalleryId(int $galleryId): int
    {
        return (int) $this->db->fetchColumn(
            'SELECT MAX(pic) FROM ' . $this->getTableName() . ' WHERE gallery_id = ?',
            [$galleryId]
        );
    }

    /**
     * @return array<array<string, mixed>>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getPicturesByGalleryId(int $galleryId): array
    {
        return $this->db->fetchAll(
            'SELECT
              p.*,
              (SELECT MIN(pmin.pic) FROM ' . $this->getTableName() . ' AS pmin WHERE pmin.gallery_id = p.gallery_id) AS `first`,
              (SELECT MAX(pmax.pic) FROM ' . $this->getTableName() . ' AS pmax WHERE pmax.gallery_id = p.gallery_id) AS `last`
            FROM
              ' . $this->getTableName() . ' AS p
            WHERE p.gallery_id = ?
            ORDER BY p.pic ASC',
            [$galleryId]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getPreviousPictureId(int $pictureNumber, int $galleryId): int
    {
        return (int) $this->db->fetchColumn(
            'SELECT id FROM ' . $this->getTableName() . ' WHERE pic < ? AND gallery_id = ? ORDER BY pic DESC LIMIT 1',
            [$pictureNumber, $galleryId]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getNextPictureId(int $pictureNumber, int $galleryId): int
    {
        return (int) $this->db->fetchColumn(
            'SELECT id FROM ' . $this->getTableName() . ' WHERE pic > ? AND gallery_id = ? ORDER BY pic ASC LIMIT 1',
            [$pictureNumber, $galleryId]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getFileById(int $pictureId): string
    {
        return $this->db->fetchColumn(
            'SELECT `file` FROM ' . $this->getTableName() . ' WHERE id = ?',
            [$pictureId]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function updatePicturesNumbers(int $pictureNumber, int $galleryId): int
    {
        return $this->db->getConnection()->executeStatement(
            'UPDATE ' . $this->getTableName() . ' SET pic = pic - 1 WHERE pic > ? AND gallery_id = ?',
            [$pictureNumber, $galleryId]
        );
    }
}
