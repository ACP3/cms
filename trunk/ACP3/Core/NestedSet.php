<?php
namespace ACP3\Core;

/**
 * Klasse zum Erstellen, Bearbeiten, Löschen und
 * Umsortieren von Knoten in einem Nested Set Baum
 *
 * @author Tino Goratsch
 */
class NestedSet
{

    /**
     * Der Tabellenname
     * @var string
     */
    private $table_name;

    /**
     * Legt fest, ob das Block-Management aktiv ist oder nicht
     * @var boolean
     */
    private $enable_blocks;

    /**
     *
     * @param string $table_name
     */
    public function __construct($table_name, $enable_blocks = false)
    {
        $this->table_name = DB_PRE . $table_name;
        $this->enable_blocks = $enable_blocks;
    }

    /**
     * Löscht einen Knoten und verschiebt seine Kinder eine Ebene nach oben
     *
     * @param integer $id
     *  Die ID des zu löschenden Datensatzes
     * @return boolean
     */
    function deleteNode($id)
    {
        if (!empty($id) && Validate::isNumber($id) === true) {
            $lr = Registry::get('Db')->fetchAssoc('SELECT left_id, right_id FROM ' . $this->table_name . ' WHERE id = ?', array($id));
            if (!empty($lr)) {
                Registry::get('Db')->beginTransaction();
                try {
                    // Die aktuelle Seite mit allen untergeordneten Seiten selektieren
                    $items = Registry::get('Db')->fetchAll('SELECT n.*, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children FROM ' . $this->table_name . ' AS p, ' . $this->table_name . ' AS n WHERE p.id = ? AND n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id ASC', array($id));
                    $c_items = count($items);

                    Registry::get('Db')->delete($this->table_name, array('id' => $id));
                    // root_id und parent_id der Kinder aktualisieren
                    for ($i = 1; $i < $c_items; ++$i) {
                        $root_id = Registry::get('Db')->fetchColumn('SELECT id FROM ' . $this->table_name . ' WHERE left_id < ? AND right_id >= ? ORDER BY left_id ASC LIMIT 1', array($items[$i]['left_id'], $items[$i]['right_id']));
                        $parent_id = Registry::get('Db')->fetchColumn('SELECT id FROM ' . $this->table_name . ' WHERE left_id < ? AND right_id >= ? ORDER BY left_id DESC LIMIT 1', array($items[$i]['left_id'], $items[$i]['right_id']));
                        Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET root_id = ?, parent_id = ?, left_id = left_id - 1, right_id = right_id - 1 WHERE id = ?', array(!empty($root_id) ? $root_id : $items[$i]['id'], !empty($parent_id) ? $parent_id : 0, $items[$i]['id']));
                    }

                    // Übergeordnete Knoten aktualiseren
                    Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET right_id = right_id - 2 WHERE left_id < ? AND right_id > ?', array($lr['left_id'], $lr['right_id']));

                    // Nachfolgende Knoten
                    Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET left_id = left_id - 2, right_id = right_id - 2 WHERE left_id > ?', array($lr['right_id']));

                    Registry::get('Db')->commit();

                    return true;
                } catch (\Exception $e) {
                    Registry::get('Db')->rollback();
                }
            }
        }
        return false;
    }

    /**
     * Erstellt einen neuen Knoten
     *
     * @param integer $parent_id
     *    ID der übergeordneten Seite
     * @param array $insert_values
     * @return boolean
     */
    function insertNode($parent_id, array $insert_values)
    {
        // Keine übergeordnete Seite zugewiesen
        if (Validate::isNumber($parent_id) === false ||
            Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . $this->table_name . ' WHERE id = ?', array($parent_id)) == 0
        ) {
            Registry::get('Db')->beginTransaction();
            try {
                // Letzten Eintrag selektieren
                if ($this->enable_blocks === true)
                    $node = Registry::get('Db')->fetchAssoc('SELECT MAX(right_id) AS right_id FROM ' . $this->table_name . ' WHERE block_id = ?', array($insert_values['block_id']));
                if ($this->enable_blocks === false || empty($node['right_id'])) {
                    $node = Registry::get('Db')->fetchAssoc('SELECT MAX(right_id) AS right_id FROM ' . $this->table_name);
                }

                // left_id und right_id Werte für das Anhängen entsprechend erhöhen
                $insert_values['left_id'] = !empty($node['right_id']) ? $node['right_id'] + 1 : 1;
                $insert_values['right_id'] = !empty($node['right_id']) ? $node['right_id'] + 2 : 2;

                Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET left_id = left_id + 2, right_id = right_id + 2 WHERE left_id >= ?', array($insert_values['left_id']));

                Registry::get('Db')->insert($this->table_name, $insert_values);
                $root_id = Registry::get('Db')->lastInsertId();
                Registry::get('Db')->update($this->table_name, array('root_id' => $root_id), array('id' => $root_id));

                Registry::get('Db')->commit();
                return true;
            } catch (\Exception $e) {
                Registry::get('Db')->rollback();
                return false;
            }
            // Übergeordnete Seite zugewiesen
        } else {
            $parent = Registry::get('Db')->fetchAssoc('SELECT root_id, left_id, right_id FROM ' . $this->table_name . ' WHERE id = ?', array($parent_id));

            Registry::get('Db')->beginTransaction();
            try {
                // Alle nachfolgenden Menüeinträge anpassen
                Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET left_id = left_id + 2, right_id = right_id + 2 WHERE left_id > ?', array($parent['right_id']));
                // Übergeordnete Menüpunkte anpassen
                Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET right_id = right_id + 2 WHERE root_id = ? AND left_id <= ? AND right_id >= ?', array($parent['root_id'], $parent['left_id'], $parent['right_id']));

                $insert_values['root_id'] = $parent['root_id'];
                $insert_values['left_id'] = $parent['right_id'];
                $insert_values['right_id'] = $parent['right_id'] + 1;

                Registry::get('Db')->insert($this->table_name, $insert_values);

                Registry::get('Db')->commit();
                return true;
            } catch (\Exception $e) {
                Registry::get('Db')->rollback();
                return false;
            }
        }
    }

    /**
     * Methode zum Bearbeiten eines Knotens
     *
     * @param integer $id
     *    ID des zu bearbeitenden Knotens
     * @param integer $parent
     *    ID des neuen Elternelements
     * @param integer $block_id
     *    ID des neuen Blocks
     * @param array $update_values
     * @return boolean
     */
    function editNode($id, $parent, $block_id, array $update_values)
    {
        if (Validate::isNumber($id) === true &&
            (Validate::isNumber($parent) === true || $parent == '') &&
            Validate::isNumber($block_id) === true
        ) {
            Registry::get('Db')->beginTransaction();
            try {
                // Die aktuelle Seite mit allen untergeordneten Seiten selektieren
                $items = Registry::get('Db')->fetchAll('SELECT n.id, n.root_id, n.left_id, n.right_id' . ($this->enable_blocks === true ? ', n.block_id' : '') . ' FROM ' . $this->table_name . ' AS p, ' . $this->table_name . ' AS n WHERE p.id = ? AND n.left_id BETWEEN p.left_id AND p.right_id ORDER BY n.left_id ASC', array($id));

                // Überprüfen, ob Seite ein Root-Element ist und ob dies auch so bleiben soll
                if (empty($parent) &&
                    ($this->enable_blocks === false || ($this->enable_blocks === true && $block_id == $items[0]['block_id'])) &&
                    Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . $this->table_name . ' WHERE left_id < ? AND right_id > ?', array($items[0]['left_id'], $items[0]['right_id'])) == 0
                ) {
                    $bool = Registry::get('Db')->update($this->table_name, $update_values, array('id' => $id));
                } else {
                    // Überprüfung, falls Seite kein Root-Element ist, aber keine Veränderung vorgenommen werden soll...
                    $chk_parent = Registry::get('Db')->fetchAssoc('SELECT id FROM ' . $this->table_name . ' WHERE left_id < ? AND right_id > ? ORDER BY left_id DESC LIMIT 1', array($items[0]['left_id'], $items[0]['right_id']));
                    if (!empty($chk_parent) && $chk_parent['id'] == $parent) {
                        $bool = Registry::get('Db')->update($this->table_name, $update_values, array('id' => $id));
                        // ...ansonsten den Baum bearbeiten...
                    } else {
                        $bool = false;
                        // Differenz zwischen linken und rechten Wert bilden
                        $page_diff = $items[0]['right_id'] - $items[0]['left_id'] + 1;

                        // Neues Elternelement
                        $new_parent = Registry::get('Db')->fetchAssoc('SELECT root_id, left_id, right_id FROM ' . $this->table_name . ' WHERE id = ?', array($parent));

                        // Knoten werden eigenes Root-Element
                        if (empty($new_parent)) {
                            $root_id = $id;
                            if ($this->enable_blocks === true) {
                                // Knoten in anderen Block verschieben
                                if ($items[0]['block_id'] != $block_id) {
                                    $new_block = Registry::get('Db')->fetchAssoc('SELECT MIN(left_id) AS left_id FROM ' . $this->table_name . ' WHERE block_id = ?', array($block_id));
                                    // Falls die Knoten in einen leeren Block verschoben werden sollen,
                                    // die right_id des letzten Elementes verwenden
                                    if (empty($new_block) || is_null($new_block['left_id']) === true) {
                                        $new_block = Registry::get('Db')->fetchAssoc('SELECT MAX(right_id) AS left_id FROM ' . $this->table_name);
                                        $new_block['left_id'] += 1;
                                    }

                                    if ($block_id > $items[0]['block_id'])
                                        $new_block['left_id'] -= $page_diff;

                                    $diff = $new_block['left_id'] - $items[0]['left_id'];

                                    Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET right_id = right_id - ? WHERE left_id < ? AND right_id > ?', array($page_diff, $items[0]['left_id'], $items[0]['right_id']));
                                    Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET left_id = left_id - ?, right_id = right_id - ? WHERE left_id > ?', array($page_diff, $page_diff, $items[0]['right_id']));
                                    Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET left_id = left_id + ?, right_id = right_id + ? WHERE left_id >= ?', array($page_diff, $page_diff, $new_block['left_id']));
                                    // Element zum neuen Wurzelknoten machen
                                } else {
                                    $max_id = Registry::get('Db')->fetchAssoc('SELECT MAX(right_id) AS right_id FROM ' . $this->table_name . ' WHERE block_id = ?', array($items[0]['block_id']));
                                    $diff = $max_id['right_id'] - $items[0]['right_id'];

                                    Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET right_id = right_id - ? WHERE left_id < ? AND right_id > ?', array($page_diff, $items[0]['left_id'], $items[0]['right_id']));
                                    Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET left_id = left_id - ?, right_id = right_id - ? WHERE left_id > ? AND block_id = ?', array($page_diff, $page_diff, $items[0]['right_id'], $items[0]['block_id']));
                                }
                            } else {
                                $max_id = Registry::get('Db')->fetchAssoc('SELECT MAX(right_id) AS right_id FROM ' . $this->table_name);
                                $diff = $max_id['right_id'] - $items[0]['right_id'];

                                Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET right_id = right_id - ? WHERE left_id < ? AND right_id > ?', array($page_diff, $items[0]['left_id'], $items[0]['right_id']));
                                Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET left_id = left_id - ?, right_id = right_id - ? WHERE left_id > ?', array($page_diff, $page_diff, $items[0]['right_id']));
                            }
                            // Knoten werden Kinder von einem anderen Knoten
                        } else {
                            // Teilbaum nach unten...
                            if ($new_parent['left_id'] > $items[0]['left_id']) {
                                $new_parent['left_id'] -= $page_diff;
                                $new_parent['right_id'] -= $page_diff;
                            }

                            $diff = $new_parent['left_id'] - $items[0]['left_id'] + 1;
                            $root_id = $new_parent['root_id'];

                            Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET right_id = right_id - ? WHERE left_id < ? AND right_id > ?', array($page_diff, $items[0]['left_id'], $items[0]['right_id']));
                            Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET left_id = left_id - ?, right_id = right_id - ? WHERE left_id > ?', array($page_diff, $page_diff, $items[0]['right_id']));
                            Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET right_id = right_id + ? WHERE left_id <= ? AND right_id >= ?', array($page_diff, $new_parent['left_id'], $new_parent['right_id']));
                            Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET left_id = left_id + ?, right_id = right_id + ? WHERE left_id > ?', array($page_diff, $page_diff, $new_parent['left_id']));
                        }

                        // Einträge aktualisieren
                        $c_items = count($items);
                        for ($i = 0; $i < $c_items; ++$i) {
                            $parent = Registry::get('Db')->fetchAssoc('SELECT id FROM ' . $this->table_name . ' WHERE left_id < ? AND right_id > ? ORDER BY left_id DESC LIMIT 1', array($items[$i]['left_id'] + $diff, $items[$i]['right_id'] + $diff));
                            if ($this->enable_blocks === true) {
                                $bool = Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET block_id = ?, root_id = ?, parent_id = ?, left_id = ?, right_id = ? WHERE id = ?', array($block_id, $root_id, !empty($parent['id']) ? $parent['id'] : 0, $items[$i]['left_id'] + $diff, $items[$i]['right_id'] + $diff, $items[$i]['id']));
                            } else {
                                $bool = Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET root_id = ?, parent_id = ?, left_id = ?, right_id = ? WHERE id = ?', array($root_id, !empty($parent['id']) ? $parent['id'] : 0, $items[$i]['left_id'] + $diff, $items[$i]['right_id'] + $diff, $items[$i]['id']));
                            }
                            if ($bool === false)
                                break;
                        }
                        Registry::get('Db')->update($this->table_name, $update_values, array('id' => $id));
                        Registry::get('Db')->commit();
                    }
                }
                return $bool;
            } catch (\Exception $e) {
                Registry::get('Db')->rollback();
            }
        }
        return false;
    }

    /**
     * Methode zum Umsortieren von Knoten
     *
     * @param integer $id
     * @param string $mode
     * @return boolean
     */
    public function order($id, $mode)
    {
        if (Validate::isNumber($id) === true && Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . $this->table_name . ' WHERE id = ?', array($id)) == 1) {
            $items = Registry::get('Db')->fetchAll('SELECT c.id, ' . ($this->enable_blocks === true ? 'c.block_id, ' : '') . 'c.left_id, c.right_id FROM ' . $this->table_name . ' AS p, ' . $this->table_name . ' AS c WHERE p.id = ? AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC', array($id), array(\PDO::PARAM_INT));

            if ($mode === 'up' && Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . $this->table_name . ' WHERE right_id = ?' . ($this->enable_blocks === true ? ' AND block_id = ' . $items[0]['block_id'] : ''), array($items[0]['left_id'] - 1)) > 0) {
                // Vorherigen Knoten mit allen Kindern selektieren
                $elem = Registry::get('Db')->fetchAll('SELECT c.id, c.left_id, c.right_id FROM ' . $this->table_name . ' AS p, ' . $this->table_name . ' AS c WHERE p.right_id = ? AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC', array($items[0]['left_id'] - 1));
                $diff_left = $items[0]['left_id'] - $elem[0]['left_id'];
                $diff_right = $items[0]['right_id'] - $elem[0]['right_id'];
            } elseif ($mode === 'down' && Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . $this->table_name . ' WHERE left_id = ?' . ($this->enable_blocks === true ? ' AND block_id = ' . $items[0]['block_id'] : ''), array($items[0]['right_id'] + 1)) > 0) {
                // Nachfolgenden Knoten mit allen Kindern selektieren
                $elem = Registry::get('Db')->fetchAll('SELECT c.id, c.left_id, c.right_id FROM ' . $this->table_name . ' AS p, ' . $this->table_name . ' AS c WHERE p.left_id = ? AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC', array($items[0]['right_id'] + 1));
                $diff_left = $elem[0]['left_id'] - $items[0]['left_id'];
                $diff_right = $elem[0]['right_id'] - $items[0]['right_id'];
            } else {
                return false;
            }

            $c_elem = count($elem);
            $c_items = count($items);
            $elem_ids = $items_ids = array();

            for ($i = 0; $i < $c_elem; ++$i) {
                $elem_ids[] = $elem[$i]['id'];
            }
            for ($i = 0; $i < $c_items; ++$i) {
                $items_ids[] = $items[$i]['id'];
            }

            Registry::get('Db')->beginTransaction();
            try {
                if ($mode === 'up') {
                    $bool = Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET left_id = left_id + ?, right_id = right_id + ? WHERE id IN(?)', array($diff_right, $diff_right, $elem_ids), array(\PDO::PARAM_INT, \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY));
                    $bool2 = Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET left_id = left_id - ?, right_id = right_id - ? WHERE id IN(?)', array($diff_left, $diff_left, $items_ids), array(\PDO::PARAM_INT, \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY));
                } else {
                    $bool = Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET left_id = left_id - ?, right_id = right_id - ? WHERE id IN(?)', array($diff_left, $diff_left, $elem_ids), array(\PDO::PARAM_INT, \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY));
                    $bool2 = Registry::get('Db')->executeUpdate('UPDATE ' . $this->table_name . ' SET left_id = left_id + ?, right_id = right_id + ? WHERE id IN(?)', array($diff_right, $diff_right, $items_ids), array(\PDO::PARAM_INT, \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY));
                }
                Registry::get('Db')->commit();
                return $bool && $bool2;
            } catch (\Exception $e) {
                Registry::get('Db')->rollback();
            }
        }
        return false;
    }
}