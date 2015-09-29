<?php

namespace ACP3\Modules\ACP3\Gallery\Model;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\ACP3\Gallery
 */
class GalleryRepository extends Core\Model
{
    const TABLE_NAME = 'gallery';

    /**
     * @param int    $id
     * @param string $time
     *
     * @return bool
     */
    public function galleryExists($id, $time = '')
    {
        $period = empty($time) === false ? ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' : '';
        return ((int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = :id' . $period, ['id' => $id, 'time' => $time]) > 0);
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getGalleryById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->getTableName() . ' WHERE id = ?', [$id]);
    }

    /**
     * @param int $galleryId
     *
     * @return mixed
     */
    public function getGalleryTitle($galleryId)
    {
        return $this->db->fetchColumn('SELECT title FROM ' . $this->getTableName() . ' WHERE id = ?', [$galleryId]);
    }

    /**
     * @param string $time
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
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT g.*, COUNT(p.gallery_id) AS pics FROM ' . $this->getTableName() . ' AS g LEFT JOIN ' . $this->getTableName(PictureRepository::TABLE_NAME) . ' AS p ON(g.id = p.gallery_id) ' . $where . ' GROUP BY g.id ORDER BY g.start DESC, g.end DESC, g.id DESC' . $limitStmt, ['time' => $time]);
    }

    /**
     * @return array
     */
    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT g.id, g.start, g.end, g.title, COUNT(p.gallery_id) AS pictures FROM ' . $this->getTableName() . ' AS g LEFT JOIN ' . $this->getTableName(PictureRepository::TABLE_NAME) . ' AS p ON(g.id = p.gallery_id) GROUP BY g.id ORDER BY g.start DESC, g.end DESC, g.id DESC');
    }
}
