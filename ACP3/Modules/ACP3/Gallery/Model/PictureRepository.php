<?php
namespace ACP3\Modules\ACP3\Gallery\Model;

use ACP3\Core;

/**
 * Class PictureRepository
 * @package ACP3\Modules\ACP3\Gallery\Model
 */
class PictureRepository extends Core\Model\AbstractRepository
{
    use Core\Model\PublicationPeriodAwareTrait;

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
        return ((int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName(GalleryRepository::TABLE_NAME) . ' AS g, ' . $this->getTableName() . ' AS p WHERE p.id = :id AND p.gallery_id = g.id' . $period, ['id' => $pictureId, 'time' => $time]) > 0);
    }

    /**
     * @param int $pictureId
     *
     * @return array
     */
    public function getPictureById($pictureId)
    {
        return $this->db->fetchAssoc('SELECT g.id AS gallery_id, g.title, p.* FROM ' . $this->getTableName(GalleryRepository::TABLE_NAME) . ' AS g, ' . $this->getTableName() . ' AS p WHERE p.id = ? AND p.gallery_id = g.id', [$pictureId]);
    }

    /**
     * @param int $pictureId
     *
     * @return mixed
     */
    public function getGalleryIdFromPictureId($pictureId)
    {
        return $this->db->fetchColumn('SELECT gallery_id FROM ' . $this->getTableName() . ' WHERE id = ?', [$pictureId]);
    }

    /**
     * @param int $galleryId
     *
     * @return mixed
     */
    public function getLastPictureByGalleryId($galleryId)
    {
        return $this->db->fetchColumn('SELECT MAX(pic) FROM ' . $this->getTableName() . ' WHERE gallery_id = ?', [$galleryId]);
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
              (SELECT pmin.pic FROM ' . $this->getTableName() . ' AS pmin WHERE pmin.gallery_id = p.gallery_id ORDER BY pmin.pic ASC LIMIT 1) AS `first`,
              (SELECT pmax.pic FROM ' . $this->getTableName() . ' AS pmax WHERE pmax.gallery_id = p.gallery_id ORDER BY pmax.pic DESC LIMIT 1) AS `last`
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
     * @return mixed
     */
    public function getPreviousPictureId($pictureNumber, $galleryId)
    {
        return $this->db->fetchColumn('SELECT id FROM ' . $this->getTableName() . ' WHERE pic < ? AND gallery_id = ? ORDER BY pic DESC LIMIT 1', [$pictureNumber, $galleryId]);
    }

    /**
     * @param int $pictureNumber
     * @param int $galleryId
     *
     * @return mixed
     */
    public function getNextPictureId($pictureNumber, $galleryId)
    {
        return $this->db->fetchColumn('SELECT id FROM ' . $this->getTableName() . ' WHERE pic > ? AND gallery_id = ? ORDER BY pic DESC LIMIT 1', [$pictureNumber, $galleryId]);
    }

    /**
     * @param int $pictureId
     *
     * @return mixed
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
