<?php

namespace ACP3\Modules\Gallery;

use ACP3\Core;

/**
 * Description of Model
 *
 * @author Tino Goratsch
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'gallery';
    const TABLE_NAME_PICTURES = 'gallery_pictures';

    public function galleryExists($id, $time = '')
    {

        $period = empty($time) === false ? ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' : '';
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = :id' . $period, array('id' => $id, 'time' => $time)) > 0 ? true : false;
    }

    public function pictureExists($pictureId, $time = '')
    {
        $period = empty($time) === false ? ' AND (g.start = g.end AND g.start <= :time OR g.start != g.end AND :time BETWEEN g.start AND g.end)' : '';
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' AS g, ' . $this->prefix . static::TABLE_NAME_PICTURES . ' AS p WHERE p.id = :id AND p.gallery_id = g.id' . $period, array('id' => $pictureId, 'time' => $time)) > 0 ? true : false;
    }

    public function getGalleryById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = ?', array($id));
    }

    public function getPictureById($id)
    {
        return $this->db->fetchAssoc('SELECT g.id AS gallery_id, g.title, p.* FROM ' . $this->prefix . static::TABLE_NAME . ' AS g, ' . $this->prefix . static::TABLE_NAME_PICTURES . ' AS p WHERE p.id = ? AND p.gallery_id = g.id', array($id));
    }

    public function getGalleryIdFromPictureId($pictureId)
    {
        return $this->db->fetchColumn('SELECT gallery_id FROM ' . $this->prefix . static::TABLE_NAME_PICTURES . ' WHERE id = ?', array($pictureId));
    }

    public function getLastPictureByGalleryId($galleryId)
    {
        return $this->db->fetchColumn('SELECT MAX(pic) FROM ' . $this->prefix . static::TABLE_NAME_PICTURES . ' WHERE gallery_id = ?', array($galleryId));
    }

    public function getPicturesByGalleryId($id)
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME_PICTURES . ' WHERE gallery_id = ? ORDER BY pic ASC', array($id));
    }

    public function getPreviousPictureId($picture, $galleryId)
    {
        return $this->db->fetchColumn('SELECT id FROM ' . $this->prefix . static::TABLE_NAME_PICTURES . ' WHERE pic < ? AND gallery_id = ? ORDER BY pic DESC LIMIT 1', array($picture, $galleryId));
    }

    public function getNextPictureId($picture, $galleryId)
    {
        return $this->db->fetchColumn('SELECT id FROM ' . $this->prefix . static::TABLE_NAME_PICTURES . ' WHERE pic > ? AND gallery_id = ? ORDER BY pic DESC LIMIT 1', array($picture, $galleryId));
    }

    public function getFileById($pictureId)
    {
        return $this->db->fetchColumn('SELECT file FROM ' . $this->prefix . static::TABLE_NAME_PICTURES . ' WHERE id = ?', array($pictureId));
    }

    public function getGalleryTitle($galleryId)
    {
        return $this->db->fetchColumn('SELECT title FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = ?', array($galleryId));
    }

    public function countAll($time)
    {
        return count($this->getAll($time));
    }

    public function getAll($time = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = $time !== '' ? ' WHERE (g.start = g.end AND g.start <= :time OR g.start != g.end AND :time BETWEEN g.start AND g.end)' : '';
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT g.*, COUNT(p.gallery_id) AS pics FROM ' . $this->prefix . static::TABLE_NAME . ' AS g LEFT JOIN ' . $this->prefix . static::TABLE_NAME_PICTURES . ' AS p ON(g.id = p.gallery_id) ' . $where . ' GROUP BY g.id ORDER BY g.start DESC, g.end DESC, g.id DESC' . $limitStmt, array('time' => $time));
    }

    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT g.id, g.start, g.end, g.title, COUNT(p.gallery_id) AS pictures FROM ' . $this->prefix . static::TABLE_NAME . ' AS g LEFT JOIN ' . $this->prefix . static::TABLE_NAME_PICTURES . ' AS p ON(g.id = p.gallery_id) GROUP BY g.id ORDER BY g.start DESC, g.end DESC, g.id DESC');
    }

    public function updatePicturesNumbers($pictureNumber, $galleryId)
    {
        return $this->db->executeUpdate('UPDATE ' . $this->prefix . static::TABLE_NAME_PICTURES . ' SET pic = pic - 1 WHERE pic > ? AND gallery_id = ?', array($pictureNumber, $galleryId));
    }

}