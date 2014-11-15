<?php

namespace ACP3\Core;

/**
 * Manages the most used functions in the ACP3
 * @package ACP3\Core
 */
class Functions
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
     * Enkodiert alle HTML-Entitäten eines Strings
     * zur Vermeidung von XSS
     *
     * @param string $var
     * @param boolean $scriptTagOnly
     *
     * @return string
     */
    public static function strEncode($var, $scriptTagOnly = false)
    {
        $var = preg_replace('=<script[^>]*>.*</script>=isU', '', $var);
        return $scriptTagOnly === true ? $var : htmlentities($var, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Verschiebt einen DB-Eintrag um einen Schritt nach oben bzw. unten
     *
     * @param string $action
     *    up = einen Schritt nach oben verschieben
     *    down = einen Schritt nach unten verschieben
     * @param string $table
     *    Die betroffene Tabelle
     * @param string $idField
     *    Name des ID-Feldes
     * @param string $sortField
     *    Name des Sortier-Feldes. damit die Sortierung geändert werden kann
     * @param string $id
     *    Die ID des Datensatzes, welcher umsortiert werden soll
     * @param string $where
     *    Optionales Vergleichsfeld, um den richtigen Vorgänger/Nachfolger bestimmen zu können
     *
     * @return boolean
     */
    public function moveOneStep($action, $table, $idField, $sortField, $id, $where = '')
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