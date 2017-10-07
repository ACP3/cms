<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Model\Repository;

use ACP3\Core;

class GalleryPicturesRepository extends Core\Model\Repository\AbstractRepository
{
    use Core\Model\Repository\PublicationPeriodAwareTrait;

    const TABLE_NAME = 'gallery_pictures';

    /**
     * @param int    $pictureId
     * @param string $time
     *
     * @return bool
     */
    public function pictureExists($pictureId, $time = '')
    {
        $period = empty($time) === false ? ' AND ' . $this->getPublicationPeriod('g.') : '';
        return ((int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName(GalleryRepository::TABLE_NAME) . ' AS g, ' . $this->getTableName() . ' AS p WHERE p.id = :id AND p.gallery_id = g.id' . $period,
                ['id' => $pictureId, 'time' => $time]) > 0);
    }

    /**
     * @inheritdoc
     */
    public function getOneById(int $entryId)
    {
        return $this->db->fetchAssoc('SELECT g.id AS gallery_id, g.title, p.* FROM ' . $this->getTableName(GalleryRepository::TABLE_NAME) . ' AS g, ' . $this->getTableName() . ' AS p WHERE p.id = ? AND p.gallery_id = g.id', [$entryId]);
    }

    /**
     * @param int $pictureId
     *
     * @return int
     */
    public function getGalleryIdFromPictureId($pictureId)
    {
        return (int)$this->db->fetchColumn('SELECT gallery_id FROM ' . $this->getTableName() . ' WHERE id = ?',
            [$pictureId]);
    }

    /**
     * @param int $galleryId
     *
     * @return int
     */
    public function getLastPictureByGalleryId($galleryId)
    {
        return (int)$this->db->fetchColumn('SELECT MAX(pic) FROM ' . $this->getTableName() . ' WHERE gallery_id = ?',
            [$galleryId]);
    }

    /**
     * @param int $galleryId
     *
     * @return array
     */
    public function getPicturesByGalleryId($galleryId)
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
     * @param int $pictureNumber
     * @param int $galleryId
     *
     * @return int
     */
    public function getPreviousPictureId($pictureNumber, $galleryId)
    {
        return (int)$this->db->fetchColumn('SELECT id FROM ' . $this->getTableName() . ' WHERE pic < ? AND gallery_id = ? ORDER BY pic DESC LIMIT 1',
            [$pictureNumber, $galleryId]);
    }

    /**
     * @param int $pictureNumber
     * @param int $galleryId
     *
     * @return int
     */
    public function getNextPictureId($pictureNumber, $galleryId)
    {
        return (int)$this->db->fetchColumn('SELECT id FROM ' . $this->getTableName() . ' WHERE pic > ? AND gallery_id = ? ORDER BY pic ASC LIMIT 1',
            [$pictureNumber, $galleryId]);
    }

    /**
     * @param int $pictureId
     *
     * @return string
     */
    public function getFileById($pictureId)
    {
        return $this->db->fetchColumn('SELECT `file` FROM ' . $this->getTableName() . ' WHERE id = ?', [$pictureId]);
    }

    /**
     * @param int $pictureNumber
     * @param int $galleryId
     *
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function updatePicturesNumbers($pictureNumber, $galleryId)
    {
        return $this->db->getConnection()->executeUpdate('UPDATE ' . $this->getTableName() . ' SET pic = pic - 1 WHERE pic > ? AND gallery_id = ?', [$pictureNumber, $galleryId]);
    }
}
