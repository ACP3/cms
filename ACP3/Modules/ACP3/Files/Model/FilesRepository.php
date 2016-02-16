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
     * @param int    $id
     * @param string $time
     *
     * @return bool
     */
    public function resultExists($id, $time = '')
    {
        $period = empty($time) === false ? ' AND ' . $this->getPublicationPeriod() : '';
        return ((int)$this->db->fetchColumn(
                'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = :id' . $period,
                ['id' => $id, 'time' => $time]
            ) > 0);
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getOneById($id)
    {
        return $this->db->fetchAssoc(
            'SELECT n.*, c.title AS category_title FROM ' . $this->getTableName() . ' AS n LEFT JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\Categories\Model\CategoryRepository::TABLE_NAME) . ' AS c ON(n.category_id = c.id) WHERE n.id = ?',
            [$id]
        );
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function getFileById($id)
    {
        return $this->db->fetchColumn('SELECT `file` FROM ' . $this->getTableName() . ' WHERE id = ?', [$id]);
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

    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT f.*, c.title AS cat FROM ' . $this->getTableName() . ' AS f, ' . $this->getTableName(\ACP3\Modules\ACP3\Categories\Model\CategoryRepository::TABLE_NAME) . ' AS c WHERE f.category_id = c.id ORDER BY f.start DESC, f.end DESC, f.id DESC');
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
