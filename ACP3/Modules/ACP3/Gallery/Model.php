<?php

namespace ACP3\Modules\ACP3\Gallery;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\ACP3\Gallery
 */
class Model extends Core\Model
{
    const TABLE_NAME = 'gallery';
    const TABLE_NAME_PICTURES = 'gallery_pictures';

    /**
     * @param        $id
     * @param string $time
     *
     * @return bool
     */
    public function galleryExists($id, $time = '')
    {
        $period = empty($time) === false ? ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' : '';
        return ((int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = :id' . $period, ['id' => $id, 'time' => $time]) > 0);
    }

    /**
     * @param        $pictureId
     * @param string $time
     *
     * @return bool
     */
    public function pictureExists($pictureId, $time = '')
    {
        $period = empty($time) === false ? ' AND (g.start = g.end AND g.start <= :time OR g.start != g.end AND :time BETWEEN g.start AND g.end)' : '';
        return ((int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS g, ' . $this->db->getPrefix() . static::TABLE_NAME_PICTURES . ' AS p WHERE p.id = :id AND p.gallery_id = g.id' . $period, ['id' => $pictureId, 'time' => $time]) > 0);
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function getGalleryById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = ?', [$id]);
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function getPictureById($id)
    {
        return $this->db->fetchAssoc('SELECT g.id AS gallery_id, g.title, p.* FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS g, ' . $this->db->getPrefix() . static::TABLE_NAME_PICTURES . ' AS p WHERE p.id = ? AND p.gallery_id = g.id', [$id]);
    }

    /**
     * @param $pictureId
     *
     * @return mixed
     */
    public function getGalleryIdFromPictureId($pictureId)
    {
        return $this->db->fetchColumn('SELECT gallery_id FROM ' . $this->db->getPrefix() . static::TABLE_NAME_PICTURES . ' WHERE id = ?', [$pictureId]);
    }

    /**
     * @param $galleryId
     *
     * @return mixed
     */
    public function getLastPictureByGalleryId($galleryId)
    {
        return $this->db->fetchColumn('SELECT MAX(pic) FROM ' . $this->db->getPrefix() . static::TABLE_NAME_PICTURES . ' WHERE gallery_id = ?', [$galleryId]);
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function getPicturesByGalleryId($id)
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME_PICTURES . ' WHERE gallery_id = ? ORDER BY pic ASC', [$id]);
    }

    /**
     * @param $picture
     * @param $galleryId
     *
     * @return mixed
     */
    public function getPreviousPictureId($picture, $galleryId)
    {
        return $this->db->fetchColumn('SELECT id FROM ' . $this->db->getPrefix() . static::TABLE_NAME_PICTURES . ' WHERE pic < ? AND gallery_id = ? ORDER BY pic DESC LIMIT 1', [$picture, $galleryId]);
    }

    /**
     * @param $picture
     * @param $galleryId
     *
     * @return mixed
     */
    public function getNextPictureId($picture, $galleryId)
    {
        return $this->db->fetchColumn('SELECT id FROM ' . $this->db->getPrefix() . static::TABLE_NAME_PICTURES . ' WHERE pic > ? AND gallery_id = ? ORDER BY pic DESC LIMIT 1', [$picture, $galleryId]);
    }

    /**
     * @param $pictureId
     *
     * @return mixed
     */
    public function getFileById($pictureId)
    {
        return $this->db->fetchColumn('SELECT file FROM ' . $this->db->getPrefix() . static::TABLE_NAME_PICTURES . ' WHERE id = ?', [$pictureId]);
    }

    /**
     * @param $galleryId
     *
     * @return mixed
     */
    public function getGalleryTitle($galleryId)
    {
        return $this->db->fetchColumn('SELECT title FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = ?', [$galleryId]);
    }

    /**
     * @param $time
     *
     * @return int
     */
    public function countAll($time)
    {
        return count($this->getAll($time));
    }

    /**
     * @param string $time
     * @param string $limitStart
     * @param string $resultsPerPage
     *
     * @return array
     */
    public function getAll($time = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = $time !== '' ? ' WHERE (g.start = g.end AND g.start <= :time OR g.start != g.end AND :time BETWEEN g.start AND g.end)' : '';
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT g.*, COUNT(p.gallery_id) AS pics FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS g LEFT JOIN ' . $this->db->getPrefix() . static::TABLE_NAME_PICTURES . ' AS p ON(g.id = p.gallery_id) ' . $where . ' GROUP BY g.id ORDER BY g.start DESC, g.end DESC, g.id DESC' . $limitStmt, ['time' => $time]);
    }

    /**
     * @return array
     */
    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT g.id, g.start, g.end, g.title, COUNT(p.gallery_id) AS pictures FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS g LEFT JOIN ' . $this->db->getPrefix() . static::TABLE_NAME_PICTURES . ' AS p ON(g.id = p.gallery_id) GROUP BY g.id ORDER BY g.start DESC, g.end DESC, g.id DESC');
    }

    /**
     * @param $pictureNumber
     * @param $galleryId
     *
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function updatePicturesNumbers($pictureNumber, $galleryId)
    {
        return $this->db->getConnection()->executeUpdate('UPDATE ' . $this->db->getPrefix() . static::TABLE_NAME_PICTURES . ' SET pic = pic - 1 WHERE pic > ? AND gallery_id = ?', [$pictureNumber, $galleryId]);
    }
}
