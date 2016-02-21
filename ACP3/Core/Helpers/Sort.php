<?php
namespace ACP3\Core\Helpers;

use ACP3\Core\DB;

/**
 * Class Sort
 * @package ACP3\Core\Helpers
 */
class Sort
{
    /**
     * @var \ACP3\Core\DB
     */
    protected $db;

    /**
     * @param \ACP3\Core\DB $db
     */
    public function __construct(DB $db)
    {
        $this->db = $db;
    }

    /**
     * Moves a database result one step upwards
     *
     * @param string $table
     * @param string $idField
     * @param string $sortField
     * @param string $id
     * @param string $where
     *
     * @return boolean
     */
    public function up($table, $idField, $sortField, $id, $where = '')
    {
        return $this->moveOneStep('up', $table, $idField, $sortField, $id, $where);
    }

    /**
     * Moves a database result one step downwards
     *
     * @param string $table
     * @param string $idField
     * @param string $sortField
     * @param string $id
     * @param string $where
     *
     * @return boolean
     */
    public function down($table, $idField, $sortField, $id, $where = '')
    {
        return $this->moveOneStep('down', $table, $idField, $sortField, $id, $where);
    }

    /**
     * Moves a database result one step upwards/downwards
     *
     * @param string $action
     * @param string $table
     * @param string $idField
     * @param string $sortField
     * @param string $id
     * @param string $where
     *
     * @return boolean
     */
    private function moveOneStep($action, $table, $idField, $sortField, $id, $where = '')
    {
        $this->db->getConnection()->beginTransaction();
        try {
            $id = (int)$id;
            $table = $this->db->getPrefix() . $table;

            // Zusätzliche WHERE-Bedingung
            $where = !empty($where) ? 'a.' . $where . ' = b.' . $where . ' AND ' : '';

            // Aktuelles Element und das vorherige Element selektieren
            $queryString = 'SELECT a.%2$s AS other_id, a.%3$s AS other_sort, b.%3$s AS elem_sort FROM %1$s AS a, %1$s AS b WHERE %5$sb.%2$s = %4$s AND a.%3$s %6$s b.%3$s ORDER BY a.%3$s %7$s LIMIT 1';

            if ($action === 'up') {
                $query = $this->db->getConnection()->fetchAssoc(sprintf($queryString, $table, $idField, $sortField, $id, $where, '<', 'DESC'));
            } else {
                $query = $this->db->getConnection()->fetchAssoc(sprintf($queryString, $table, $idField, $sortField, $id, $where, '>', 'ASC'));
            }

            if (!empty($query)) {
                // Sortierreihenfolge des aktuellen Elementes zunächst auf 0 setzen
                // um Probleme mit möglichen Duplicate-Keys zu umgehen
                $this->db->getConnection()->update($table, [$sortField => 0], [$idField => $id]);
                $this->db->getConnection()->update($table, [$sortField => $query['elem_sort']], [$idField => $query['other_id']]);
                // Element nun den richtigen Wert zuweisen
                $this->db->getConnection()->update($table, [$sortField => $query['other_sort']], [$idField => $id]);

                $this->db->getConnection()->commit();
                return true;
            }
        } catch (\Exception $e) {
            $this->db->getConnection()->rollback();
        }

        return false;
    }
}
