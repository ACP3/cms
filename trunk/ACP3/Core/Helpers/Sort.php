<?php
namespace ACP3\Core\Helpers;

/**
 * Class Sort
 * @package ACP3\Core\Helpers
 */
class Sort
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;

    /**
     * @param \Doctrine\DBAL\Connection $db
     */
    public function __construct(\Doctrine\DBAL\Connection $db)
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
     * @return boolean
     */
    public function up($table, $idField, $sortField, $id, $where = '')
    {
        return $this->_moveOneStep('up', $table, $idField, $sortField, $id, $where);
    }

    /**
     * Moves a database result one step downwards
     *
     * @param string $table
     * @param string $idField
     * @param string $sortField
     * @param string $id
     * @param string $where
     * @return boolean
     */
    public function down($table, $idField, $sortField, $id, $where = '')
    {
        return $this->_moveOneStep('down', $table, $idField, $sortField, $id, $where);
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
     * @return boolean
     */
    private function _moveOneStep($action, $table, $idField, $sortField, $id, $where = '')
    {
        if ($action === 'up' || $action === 'down') {
            $this->db->beginTransaction();
            try {
                $id = (int)$id;
                $table = DB_PRE . $table;

                // Zusätzliche WHERE-Bedingung
                $where = !empty($where) ? 'a.' . $where . ' = b.' . $where . ' AND ' : '';

                // Ein Schritt nach oben
                if ($action === 'up') {
                    // Aktuelles Element und das vorherige Element selektieren
                    $query = $this->db->fetchAssoc('SELECT a.' . $idField . ' AS other_id, a.' . $sortField . ' AS other_sort, b.' . $sortField . ' AS elem_sort FROM ' . $table . ' AS a, ' . $table . ' AS b WHERE ' . $where . 'b.' . $idField . ' = ' . $id . ' AND a.' . $sortField . ' < b.' . $sortField . ' ORDER BY a.' . $sortField . ' DESC LIMIT 1');
                    // Ein Schritt nach unten
                } else {
                    // Aktuelles Element und das nachfolgende Element selektieren
                    $query = $this->db->fetchAssoc('SELECT a.' . $idField . ' AS other_id, a.' . $sortField . ' AS other_sort, b.' . $sortField . ' AS elem_sort FROM ' . $table . ' AS a, ' . $table . ' AS b WHERE ' . $where . 'b.' . $idField . ' = ' . $id . ' AND a.' . $sortField . ' > b.' . $sortField . ' ORDER BY a.' . $sortField . ' ASC LIMIT 1');
                }

                if (!empty($query)) {
                    // Sortierreihenfolge des aktuellen Elementes zunächst auf 0 setzen
                    // um Probleme mit möglichen Duplicate-Keys zu umgehen
                    $this->db->update($table, array($sortField => 0), array($idField => $id));
                    $this->db->update($table, array($sortField => $query['elem_sort']), array($idField => $query['other_id']));
                    // Element nun den richtigen Wert zuweisen
                    $this->db->update($table, array($sortField => $query['other_sort']), array($idField => $id));

                    $this->db->commit();
                    return true;
                }
            } catch (\Exception $e) {
                $this->db->rollback();
            }
        }
        return false;
    }
} 