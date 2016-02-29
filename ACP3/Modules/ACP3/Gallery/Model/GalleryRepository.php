<?php

namespace ACP3\Modules\ACP3\Gallery\Model;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\ACP3\Gallery
 */
class GalleryRepository extends Core\Model\AbstractRepository
{
    use Core\Model\PublicationPeriodAwareTrait;

    const TABLE_NAME = 'gallery';

    /**
     * @param int    $galleryId
     * @param string $time
     *
     * @return bool
     */
    public function galleryExists($galleryId, $time = '')
    {
        $period = empty($time) === false ? ' AND ' . $this->getPublicationPeriod() : '';
        return ((int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = :id' . $period,
                ['id' => $galleryId, 'time' => $time]) > 0);
    }

    /**
     * @param int $galleryId
     *
     * @return array
     */
    public function getGalleryById($galleryId)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->getTableName() . ' WHERE id = ?', [$galleryId]);
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
        $where = $time !== '' ? ' WHERE ' . $this->getPublicationPeriod() : '';
        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()}{$where}",
            ['time' => $time]
        );
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
        $where = $time !== '' ? ' WHERE ' . $this->getPublicationPeriod('g.') : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll(
            'SELECT g.*, COUNT(p.gallery_id) AS pics FROM ' . $this->getTableName() . ' AS g LEFT JOIN ' . $this->getTableName(PictureRepository::TABLE_NAME) . ' AS p ON(g.id = p.gallery_id) ' . $where . ' GROUP BY g.id ORDER BY g.start DESC, g.end DESC, g.id DESC' . $limitStmt,
            ['time' => $time]
        );
    }

    /**
     * @return array
     */
    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT g.id, g.start, g.end, g.title, COUNT(p.gallery_id) AS pictures FROM ' . $this->getTableName() . ' AS g LEFT JOIN ' . $this->getTableName(PictureRepository::TABLE_NAME) . ' AS p ON(g.id = p.gallery_id) GROUP BY g.id ORDER BY g.start DESC, g.end DESC, g.id DESC');
    }
}
