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
     * Liefert ein Array zur Ausgabe als Dropdown-Menü
     * für die Anzahl der anzuzeigenden Datensätze je Seite
     *
     * @param integer $currentValue
     * @param integer $steps
     * @param integer $maxValue
     *
     * @return array
     */
    public static function recordsPerPage($currentValue, $steps = 5, $maxValue = 50)
    {
        $records = [];
        for ($i = 0, $j = $steps; $j <= $maxValue; $i++, $j += $steps) {
            $records[$i]['value'] = $j;
            $records[$i]['selected'] = self::selectEntry('entries', $j, $currentValue);
        }
        return $records;
    }

    /**
     * Selektion eines Eintrages in einem Dropdown-Menü
     *
     * @param string $name
     *  Name des Feldes im Formular
     * @param mixed  $defValue
     *  Abzugleichender Parameter mit $currentValue
     * @param mixed  $currentValue
     *  Wert aus der SQL Tabelle
     * @param string $attr
     *  HTML-Attribut, um Eintrag zu selektieren
     *
     * @return string
     */
    public static function selectEntry($name, $defValue, $currentValue = '', $attr = 'selected')
    {
        $attr = ' ' . $attr . '="' . $attr . '"';

        if (isset($_POST[$name]) === true) {
            $currentValue = $_POST[$name];
        }

        if (is_array($currentValue) === false && $currentValue == $defValue) {
            return $attr;
        } elseif (is_array($currentValue) === true) {
            foreach ($currentValue as $row) {
                if ($row == $defValue) {
                    return $attr;
                }
            }
        }

        return '';
    }

    /**
     *
     * @param string         $name
     * @param array          $values
     * @param array          $lang
     * @param string|integer $currentValue
     * @param string         $selected
     *
     * @return array
     */
    public static function selectGenerator($name, array $values, array $lang, $currentValue = '', $selected = 'selected')
    {
        $array = [];
        if (count($values) == count($lang)) {
            $c_array = count($values);
            $id = str_replace('_', '-', $name);
            for ($i = 0; $i < $c_array; ++$i) {
                $array[] = array(
                    'value' => $values[$i],
                    'id' => ($selected == 'checked' ? $id . '-' . $values[$i] : ''),
                    $selected => self::selectEntry($name, $values[$i], $currentValue, $selected),
                    'lang' => $lang[$i]
                );
            }
        }
        return $array;
    }

    /**
     * Enkodiert alle HTML-Entitäten eines Strings
     * zur Vermeidung von XSS
     *
     * @param string  $var
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