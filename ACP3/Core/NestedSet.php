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
    protected $tableName;

    /**
     * Legt fest, ob das Block-Management aktiv ist oder nicht
     * @var boolean
     */
    protected $enableBlocks;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;

    /**
     *
     * @param \Doctrine\DBAL\Connection $db
     * @param string $tableName
     * @param bool $enableBlocks
     */
    public function __construct(\Doctrine\DBAL\Connection $db, $tableName, $enableBlocks = false)
    {
        $this->db = $db;
        $this->tableName = DB_PRE . $tableName;
        $this->enableBlocks = $enableBlocks;
    }

    /**
     * Löscht einen Knoten und verschiebt seine Kinder eine Ebene nach oben
     *
     * @param integer $id
     *  Die ID des zu löschenden Datensatzes
     * @return boolean
     */
    public function deleteNode($id)
    {
        if (!empty($id) && Validate::isNumber($id) === true) {
            $lr = $this->db->fetchAssoc('SELECT left_id, right_id FROM ' . $this->tableName . ' WHERE id = ?', array($id));
            if (!empty($lr)) {
                $this->db->beginTransaction();
                try {
                    // Die aktuelle Seite mit allen untergeordneten Seiten selektieren
                    $items = $this->db->fetchAll('SELECT n.*, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children FROM ' . $this->tableName . ' AS p, ' . $this->tableName . ' AS n WHERE p.id = ? AND n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id ASC', array($id));
                    $c_items = count($items);

                    $this->db->delete($this->tableName, array('id' => $id));
                    // root_id und parent_id der Kinder aktualisieren
                    for ($i = 1; $i < $c_items; ++$i) {
                        $root_id = $this->db->fetchColumn('SELECT id FROM ' . $this->tableName . ' WHERE left_id < ? AND right_id >= ? ORDER BY left_id ASC LIMIT 1', array($items[$i]['left_id'], $items[$i]['right_id']));
                        $parent_id = $this->db->fetchColumn('SELECT id FROM ' . $this->tableName . ' WHERE left_id < ? AND right_id >= ? ORDER BY left_id DESC LIMIT 1', array($items[$i]['left_id'], $items[$i]['right_id']));
                        $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET root_id = ?, parent_id = ?, left_id = left_id - 1, right_id = right_id - 1 WHERE id = ?', array(!empty($root_id) ? $root_id : $items[$i]['id'], !empty($parent_id) ? $parent_id : 0, $items[$i]['id']));
                    }

                    // Übergeordnete Knoten aktualiseren
                    $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET right_id = right_id - 2 WHERE left_id < ? AND right_id > ?', array($lr['left_id'], $lr['right_id']));

                    // Nachfolgende Knoten
                    $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id - 2, right_id = right_id - 2 WHERE left_id > ?', array($lr['right_id']));

                    $this->db->commit();

                    return true;
                } catch (\Exception $e) {
                    $this->db->rollback();
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
    public function insertNode($parent_id, array $insert_values)
    {
        // Keine übergeordnete Seite zugewiesen
        if (Validate::isNumber($parent_id) === false ||
            $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->tableName . ' WHERE id = ?', array($parent_id)) == 0
        ) {
            $this->db->beginTransaction();
            try {
                // Letzten Eintrag selektieren
                if ($this->enableBlocks === true)
                    $node = $this->db->fetchAssoc('SELECT MAX(right_id) AS right_id FROM ' . $this->tableName . ' WHERE block_id = ?', array($insert_values['block_id']));
                if ($this->enableBlocks === false || empty($node['right_id'])) {
                    $node = $this->db->fetchAssoc('SELECT MAX(right_id) AS right_id FROM ' . $this->tableName);
                }

                // left_id und right_id Werte für das Anhängen entsprechend erhöhen
                $insert_values['left_id'] = !empty($node['right_id']) ? $node['right_id'] + 1 : 1;
                $insert_values['right_id'] = !empty($node['right_id']) ? $node['right_id'] + 2 : 2;

                $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id + 2, right_id = right_id + 2 WHERE left_id >= ?', array($insert_values['left_id']));

                $this->db->insert($this->tableName, $insert_values);
                $root_id = $this->db->lastInsertId();
                $this->db->update($this->tableName, array('root_id' => $root_id), array('id' => $root_id));

                $this->db->commit();
                return true;
            } catch (\Exception $e) {
                $this->db->rollback();
                return false;
            }
            // Übergeordnete Seite zugewiesen
        } else {
            $parent = $this->db->fetchAssoc('SELECT root_id, left_id, right_id FROM ' . $this->tableName . ' WHERE id = ?', array($parent_id));

            $this->db->beginTransaction();
            try {
                // Alle nachfolgenden Menüeinträge anpassen
                $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id + 2, right_id = right_id + 2 WHERE left_id > ?', array($parent['right_id']));
                // Übergeordnete Menüpunkte anpassen
                $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET right_id = right_id + 2 WHERE root_id = ? AND left_id <= ? AND right_id >= ?', array($parent['root_id'], $parent['left_id'], $parent['right_id']));

                $insert_values['root_id'] = $parent['root_id'];
                $insert_values['left_id'] = $parent['right_id'];
                $insert_values['right_id'] = $parent['right_id'] + 1;

                $this->db->insert($this->tableName, $insert_values);

                $this->db->commit();
                return true;
            } catch (\Exception $e) {
                $this->db->rollback();
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
    public function editNode($id, $parent, $block_id, array $update_values)
    {
        if (Validate::isNumber($id) === true &&
            (Validate::isNumber($parent) === true || $parent == '') &&
            Validate::isNumber($block_id) === true
        ) {
            $this->db->beginTransaction();
            try {
                // Die aktuelle Seite mit allen untergeordneten Seiten selektieren
                $items = $this->db->fetchAll('SELECT n.id, n.root_id, n.left_id, n.right_id' . ($this->enableBlocks === true ? ', n.block_id' : '') . ' FROM ' . $this->tableName . ' AS p, ' . $this->tableName . ' AS n WHERE p.id = ? AND n.left_id BETWEEN p.left_id AND p.right_id ORDER BY n.left_id ASC', array($id));

                // Überprüfen, ob Seite ein Root-Element ist und ob dies auch so bleiben soll
                if (empty($parent) &&
                    ($this->enableBlocks === false || ($this->enableBlocks === true && $block_id == $items[0]['block_id'])) &&
                    $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->tableName . ' WHERE left_id < ? AND right_id > ?', array($items[0]['left_id'], $items[0]['right_id'])) == 0
                ) {
                    $bool = $this->db->update($this->tableName, $update_values, array('id' => $id));
                } else {
                    // Überprüfung, falls Seite kein Root-Element ist, aber keine Veränderung vorgenommen werden soll...
                    $chk_parent = $this->db->fetchAssoc('SELECT id FROM ' . $this->tableName . ' WHERE left_id < ? AND right_id > ? ORDER BY left_id DESC LIMIT 1', array($items[0]['left_id'], $items[0]['right_id']));
                    if (!empty($chk_parent) && $chk_parent['id'] == $parent) {
                        $bool = $this->db->update($this->tableName, $update_values, array('id' => $id));
                        // ...ansonsten den Baum bearbeiten...
                    } else {
                        $bool = false;
                        // Differenz zwischen linken und rechten Wert bilden
                        $page_diff = $items[0]['right_id'] - $items[0]['left_id'] + 1;

                        // Neues Elternelement
                        $new_parent = $this->db->fetchAssoc('SELECT root_id, left_id, right_id FROM ' . $this->tableName . ' WHERE id = ?', array($parent));

                        // Knoten werden eigenes Root-Element
                        if (empty($new_parent)) {
                            $root_id = $id;
                            if ($this->enableBlocks === true) {
                                // Knoten in anderen Block verschieben
                                if ($items[0]['block_id'] != $block_id) {
                                    $new_block = $this->db->fetchAssoc('SELECT MIN(left_id) AS left_id FROM ' . $this->tableName . ' WHERE block_id = ?', array($block_id));
                                    // Falls die Knoten in einen leeren Block verschoben werden sollen,
                                    // die right_id des letzten Elementes verwenden
                                    if (empty($new_block) || is_null($new_block['left_id']) === true) {
                                        $new_block = $this->db->fetchAssoc('SELECT MAX(right_id) AS left_id FROM ' . $this->tableName);
                                        $new_block['left_id'] += 1;
                                    }

                                    if ($block_id > $items[0]['block_id'])
                                        $new_block['left_id'] -= $page_diff;

                                    $diff = $new_block['left_id'] - $items[0]['left_id'];

                                    $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET right_id = right_id - ? WHERE left_id < ? AND right_id > ?', array($page_diff, $items[0]['left_id'], $items[0]['right_id']));
                                    $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id - ?, right_id = right_id - ? WHERE left_id > ?', array($page_diff, $page_diff, $items[0]['right_id']));
                                    $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id + ?, right_id = right_id + ? WHERE left_id >= ?', array($page_diff, $page_diff, $new_block['left_id']));
                                    // Element zum neuen Wurzelknoten machen
                                } else {
                                    $max_id = $this->db->fetchAssoc('SELECT MAX(right_id) AS right_id FROM ' . $this->tableName . ' WHERE block_id = ?', array($items[0]['block_id']));
                                    $diff = $max_id['right_id'] - $items[0]['right_id'];

                                    $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET right_id = right_id - ? WHERE left_id < ? AND right_id > ?', array($page_diff, $items[0]['left_id'], $items[0]['right_id']));
                                    $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id - ?, right_id = right_id - ? WHERE left_id > ? AND block_id = ?', array($page_diff, $page_diff, $items[0]['right_id'], $items[0]['block_id']));
                                }
                            } else {
                                $max_id = $this->db->fetchAssoc('SELECT MAX(right_id) AS right_id FROM ' . $this->tableName);
                                $diff = $max_id['right_id'] - $items[0]['right_id'];

                                $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET right_id = right_id - ? WHERE left_id < ? AND right_id > ?', array($page_diff, $items[0]['left_id'], $items[0]['right_id']));
                                $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id - ?, right_id = right_id - ? WHERE left_id > ?', array($page_diff, $page_diff, $items[0]['right_id']));
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

                            $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET right_id = right_id - ? WHERE left_id < ? AND right_id > ?', array($page_diff, $items[0]['left_id'], $items[0]['right_id']));
                            $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id - ?, right_id = right_id - ? WHERE left_id > ?', array($page_diff, $page_diff, $items[0]['right_id']));
                            $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET right_id = right_id + ? WHERE left_id <= ? AND right_id >= ?', array($page_diff, $new_parent['left_id'], $new_parent['right_id']));
                            $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id + ?, right_id = right_id + ? WHERE left_id > ?', array($page_diff, $page_diff, $new_parent['left_id']));
                        }

                        // Einträge aktualisieren
                        $c_items = count($items);
                        for ($i = 0; $i < $c_items; ++$i) {
                            $parent = $this->db->fetchAssoc('SELECT id FROM ' . $this->tableName . ' WHERE left_id < ? AND right_id > ? ORDER BY left_id DESC LIMIT 1', array($items[$i]['left_id'] + $diff, $items[$i]['right_id'] + $diff));
                            if ($this->enableBlocks === true) {
                                $bool = $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET block_id = ?, root_id = ?, parent_id = ?, left_id = ?, right_id = ? WHERE id = ?', array($block_id, $root_id, !empty($parent['id']) ? $parent['id'] : 0, $items[$i]['left_id'] + $diff, $items[$i]['right_id'] + $diff, $items[$i]['id']));
                            } else {
                                $bool = $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET root_id = ?, parent_id = ?, left_id = ?, right_id = ? WHERE id = ?', array($root_id, !empty($parent['id']) ? $parent['id'] : 0, $items[$i]['left_id'] + $diff, $items[$i]['right_id'] + $diff, $items[$i]['id']));
                            }
                            if ($bool === false)
                                break;
                        }
                        $this->db->update($this->tableName, $update_values, array('id' => $id));
                        $this->db->commit();
                    }
                }
                return $bool;
            } catch (\Exception $e) {
                $this->db->rollback();
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
        if (Validate::isNumber($id) === true && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->tableName . ' WHERE id = ?', array($id)) == 1) {
            $items = $this->db->fetchAll('SELECT c.id, ' . ($this->enableBlocks === true ? 'c.block_id, ' : '') . 'c.left_id, c.right_id FROM ' . $this->tableName . ' AS p, ' . $this->tableName . ' AS c WHERE p.id = ? AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC', array($id), array(\PDO::PARAM_INT));

            if ($mode === 'up' && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->tableName . ' WHERE right_id = ?' . ($this->enableBlocks === true ? ' AND block_id = ' . $items[0]['block_id'] : ''), array($items[0]['left_id'] - 1)) > 0) {
                // Vorherigen Knoten mit allen Kindern selektieren
                $elem = $this->db->fetchAll('SELECT c.id, c.left_id, c.right_id FROM ' . $this->tableName . ' AS p, ' . $this->tableName . ' AS c WHERE p.right_id = ? AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC', array($items[0]['left_id'] - 1));
                $diff_left = $items[0]['left_id'] - $elem[0]['left_id'];
                $diff_right = $items[0]['right_id'] - $elem[0]['right_id'];
            } elseif ($mode === 'down' && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->tableName . ' WHERE left_id = ?' . ($this->enableBlocks === true ? ' AND block_id = ' . $items[0]['block_id'] : ''), array($items[0]['right_id'] + 1)) > 0) {
                // Nachfolgenden Knoten mit allen Kindern selektieren
                $elem = $this->db->fetchAll('SELECT c.id, c.left_id, c.right_id FROM ' . $this->tableName . ' AS p, ' . $this->tableName . ' AS c WHERE p.left_id = ? AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC', array($items[0]['right_id'] + 1));
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

            $this->db->beginTransaction();
            try {
                if ($mode === 'up') {
                    $bool = $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id + ?, right_id = right_id + ? WHERE id IN(?)', array($diff_right, $diff_right, $elem_ids), array(\PDO::PARAM_INT, \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY));
                    $bool2 = $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id - ?, right_id = right_id - ? WHERE id IN(?)', array($diff_left, $diff_left, $items_ids), array(\PDO::PARAM_INT, \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY));
                } else {
                    $bool = $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id - ?, right_id = right_id - ? WHERE id IN(?)', array($diff_left, $diff_left, $elem_ids), array(\PDO::PARAM_INT, \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY));
                    $bool2 = $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id + ?, right_id = right_id + ? WHERE id IN(?)', array($diff_right, $diff_right, $items_ids), array(\PDO::PARAM_INT, \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY));
                }
                $this->db->commit();
                return $bool && $bool2;
            } catch (\Exception $e) {
                $this->db->rollback();
            }
        }
        return false;
    }
}