<?php

namespace ACP3\Modules\ACP3\Files\Model;

use ACP3\Core;

/**
 * Class FilesRepository
 * @package ACP3\Modules\ACP3\Files\Model
 */
class FilesRepository extends Core\Model\AbstractRepository
{
    use Core\Model\PublicationPeriodAwareTrait;

    const TABLE_NAME = 'files';

    /**
     * @param int    $fileId
     * @param string $time
     *
     * @return bool
     */
    public function resultExists($fileId, $time = '')
    {
        $period = empty($time) === false ? ' AND ' . $this->getPublicationPeriod() : '';
        return ((int)$this->db->fetchColumn(
                'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = :id' . $period,
                ['id' => $fileId, 'time' => $time]
            ) > 0);
    }

    /**
     * @param int $fileId
     *
     * @return array
     */
    public function getOneById($fileId)
    {
        return $this->db->fetchAssoc(
            'SELECT n.*, c.title AS category_title FROM ' . $this->getTableName() . ' AS n LEFT JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\Categories\Model\CategoryRepository::TABLE_NAME) . ' AS c ON(n.category_id = c.id) WHERE n.id = ?',
            [$fileId]
        );
    }

    /**
     * @param int $fileId
     *
     * @return mixed
     */
    public function getFileById($fileId)
    {
        return $this->db->fetchColumn('SELECT `file` FROM ' . $this->getTableName() . ' WHERE id = ?', [$fileId]);
    }

    /**
     * @param string $time
     * @param string $categoryId
     *
     * @return int
     */
    public function countAll($time = '', $categoryId = '')
    {
        if (!empty($categoryId)) {
            $results = $this->getAllByCategoryId($categoryId, $time);
        } else {
            $results = $this->getAll($time);
        }

        return count($results);
    }

    /**
     * @param int    $categoryId
     * @param string $time
     * @param string $limitStart
     * @param string $resultsPerPage
     *
     * @return array
     */
    public function getAllByCategoryId($categoryId, $time = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($time) === false ? ' AND ' . $this->getPublicationPeriod() : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll(
            'SELECT * FROM ' . $this->getTableName() . ' WHERE category_id = :categoryId' . $where . ' ORDER BY `start` DESC, `end` DESC, `id` DESC' . $limitStmt,
            ['time' => $time, 'categoryId' => $categoryId]
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
        $where = empty($time) === false ? ' WHERE ' . $this->getPublicationPeriod() : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll(
            'SELECT * FROM ' . $this->getTableName() . $where . ' ORDER BY `start` DESC, `end` DESC, `id` DESC' . $limitStmt,
            ['time' => $time]
        );
    }

    /**
     * @param string $fields
     * @param string $searchTerm
     * @param string $sortDirection
     * @param string $time
     *
     * @return array
     */
    public function getAllSearchResults($fields, $searchTerm, $sortDirection, $time)
    {
        $period = ' AND ' . $this->getPublicationPeriod();
        return $this->db->fetchAll(
            'SELECT id, title, `text` FROM ' . $this->getTableName() . ' WHERE MATCH (' . $fields . ') AGAINST (' . $this->db->getConnection()->quote($searchTerm) . ' IN BOOLEAN MODE)' . $period . ' ORDER BY `start` ' . $sortDirection . ', `end` ' . $sortDirection . ', id ' . $sortDirection,
            ['time' => $time]
        );
    }
}
