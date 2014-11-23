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
     * @var DB
     */
    protected $db;

    /**
     * @param DB $db
     * @param $tableName
     * @param bool $enableBlocks
     */
    public function __construct(
        DB $db,
        $tableName,
        $enableBlocks = false
    )
    {
        $this->db = $db->getConnection();
        $this->tableName = $db->getPrefix() . $tableName;
        $this->enableBlocks = $enableBlocks;
    }

    /**
     * Löscht einen Knoten und verschiebt seine Kinder eine Ebene nach oben
     *
     * @param integer $id
     *  Die ID des zu löschenden Datensatzes
     *
     * @return boolean
     */
    public function deleteNode($id)
    {
        $id = (int)$id;
        if (!empty($id)) {
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
     * @param integer $parentId
     *    ID der übergeordneten Seite
     * @param array $insertValues
     *
     * @return boolean
     */
    public function insertNode($parentId, array $insertValues)
    {
        $parentId = (int)$parentId;
        // Keine übergeordnete Seite zugewiesen
        if (empty($parentId) ||
            $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->tableName . ' WHERE id = ?', array($parentId)) == 0
        ) {
            $this->db->beginTransaction();
            try {
                // Letzten Eintrag selektieren
                if ($this->enableBlocks === true)
                    $node = $this->db->fetchAssoc('SELECT MAX(right_id) AS right_id FROM ' . $this->tableName . ' WHERE block_id = ?', array($insertValues['block_id']));
                if ($this->enableBlocks === false || empty($node['right_id'])) {
                    $node = $this->db->fetchAssoc('SELECT MAX(right_id) AS right_id FROM ' . $this->tableName);
                }

                // left_id und right_id Werte für das Anhängen entsprechend erhöhen
                $insertValues['left_id'] = !empty($node['right_id']) ? $node['right_id'] + 1 : 1;
                $insertValues['right_id'] = !empty($node['right_id']) ? $node['right_id'] + 2 : 2;

                $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id + 2, right_id = right_id + 2 WHERE left_id >= ?', array($insertValues['left_id']));

                $this->db->insert($this->tableName, $insertValues);
                $rootId = $this->db->lastInsertId();
                $this->db->update($this->tableName, array('root_id' => $rootId), array('id' => $rootId));

                $this->db->commit();
                return true;
            } catch (\Exception $e) {
                $this->db->rollback();
                return false;
            }
            // Übergeordnete Seite zugewiesen
        } else {
            $parent = $this->db->fetchAssoc('SELECT root_id, left_id, right_id FROM ' . $this->tableName . ' WHERE id = ?', array($parentId));

            $this->db->beginTransaction();
            try {
                // Alle nachfolgenden Menüeinträge anpassen
                $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id + 2, right_id = right_id + 2 WHERE left_id > ?', array($parent['right_id']));
                // Übergeordnete Menüpunkte anpassen
                $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET right_id = right_id + 2 WHERE root_id = ? AND left_id <= ? AND right_id >= ?', array($parent['root_id'], $parent['left_id'], $parent['right_id']));

                $insertValues['root_id'] = $parent['root_id'];
                $insertValues['left_id'] = $parent['right_id'];
                $insertValues['right_id'] = $parent['right_id'] + 1;

                $this->db->insert($this->tableName, $insertValues);

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
     * @param integer $blockId
     *    ID des neuen Blocks
     * @param array $updateValues
     *
     * @return boolean
     */
    public function editNode($id, $parent, $blockId, array $updateValues)
    {
        $this->db->beginTransaction();
        try {
            // Die aktuelle Seite mit allen untergeordneten Seiten selektieren
            $items = $this->db->fetchAll('SELECT n.id, n.root_id, n.left_id, n.right_id' . ($this->enableBlocks === true ? ', n.block_id' : '') . ' FROM ' . $this->tableName . ' AS p, ' . $this->tableName . ' AS n WHERE p.id = ? AND n.left_id BETWEEN p.left_id AND p.right_id ORDER BY n.left_id ASC', array($id));

            // Überprüfen, ob Seite ein Root-Element ist und ob dies auch so bleiben soll
            if (empty($parent) &&
                ($this->enableBlocks === false || ($this->enableBlocks === true && $blockId == $items[0]['block_id'])) &&
                $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->tableName . ' WHERE left_id < ? AND right_id > ?', array($items[0]['left_id'], $items[0]['right_id'])) == 0
            ) {
                $bool = $this->db->update($this->tableName, $updateValues, array('id' => $id));
            } else {
                // Überprüfung, falls Seite kein Root-Element ist, aber keine Veränderung vorgenommen werden soll...
                $checkParent = $this->db->fetchAssoc('SELECT id FROM ' . $this->tableName . ' WHERE left_id < ? AND right_id > ? ORDER BY left_id DESC LIMIT 1', array($items[0]['left_id'], $items[0]['right_id']));
                if (!empty($checkParent) && $checkParent['id'] == $parent) {
                    $bool = $this->db->update($this->tableName, $updateValues, array('id' => $id));
                    // ...ansonsten den Baum bearbeiten...
                } else {
                    $bool = false;
                    // Differenz zwischen linken und rechten Wert bilden
                    $pageDiff = $items[0]['right_id'] - $items[0]['left_id'] + 1;

                    // Neues Elternelement
                    $newParent = $this->db->fetchAssoc('SELECT root_id, left_id, right_id FROM ' . $this->tableName . ' WHERE id = ?', array($parent));

                    // Knoten werden eigenes Root-Element
                    if (empty($newParent)) {
                        $rootId = $id;
                        if ($this->enableBlocks === true) {
                            // Knoten in anderen Block verschieben
                            if ($items[0]['block_id'] != $blockId) {
                                $newBlock = $this->db->fetchAssoc('SELECT MIN(left_id) AS left_id FROM ' . $this->tableName . ' WHERE block_id = ?', array($blockId));
                                // Falls die Knoten in einen leeren Block verschoben werden sollen,
                                // die right_id des letzten Elementes verwenden
                                if (empty($newBlock) || is_null($newBlock['left_id']) === true) {
                                    $newBlock = $this->db->fetchAssoc('SELECT MAX(right_id) AS left_id FROM ' . $this->tableName);
                                    $newBlock['left_id'] += 1;
                                }

                                if ($blockId > $items[0]['block_id'])
                                    $newBlock['left_id'] -= $pageDiff;

                                $diff = $newBlock['left_id'] - $items[0]['left_id'];

                                $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET right_id = right_id - ? WHERE left_id < ? AND right_id > ?', array($pageDiff, $items[0]['left_id'], $items[0]['right_id']));
                                $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id - ?, right_id = right_id - ? WHERE left_id > ?', array($pageDiff, $pageDiff, $items[0]['right_id']));
                                $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id + ?, right_id = right_id + ? WHERE left_id >= ?', array($pageDiff, $pageDiff, $newBlock['left_id']));
                                // Element zum neuen Wurzelknoten machen
                            } else {
                                $maxId = $this->db->fetchColumn('SELECT MAX(right_id) AS right_id FROM ' . $this->tableName . ' WHERE block_id = ?', array($items[0]['block_id']));
                                $diff = $maxId - $items[0]['right_id'];

                                $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET right_id = right_id - ? WHERE left_id < ? AND right_id > ?', array($pageDiff, $items[0]['left_id'], $items[0]['right_id']));
                                $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id - ?, right_id = right_id - ? WHERE left_id > ? AND block_id = ?', array($pageDiff, $pageDiff, $items[0]['right_id'], $items[0]['block_id']));
                            }
                        } else {
                            $maxId = $this->db->fetchColumn('SELECT MAX(right_id) AS right_id FROM ' . $this->tableName);
                            $diff = $maxId - $items[0]['right_id'];

                            $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET right_id = right_id - ? WHERE left_id < ? AND right_id > ?', array($pageDiff, $items[0]['left_id'], $items[0]['right_id']));
                            $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id - ?, right_id = right_id - ? WHERE left_id > ?', array($pageDiff, $pageDiff, $items[0]['right_id']));
                        }
                        // Knoten werden Kinder von einem anderen Knoten
                    } else {
                        // Teilbaum nach unten...
                        if ($newParent['left_id'] > $items[0]['left_id']) {
                            $newParent['left_id'] -= $pageDiff;
                            $newParent['right_id'] -= $pageDiff;
                        }

                        $diff = $newParent['left_id'] - $items[0]['left_id'] + 1;
                        $rootId = $newParent['root_id'];

                        $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET right_id = right_id - ? WHERE left_id < ? AND right_id > ?', array($pageDiff, $items[0]['left_id'], $items[0]['right_id']));
                        $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id - ?, right_id = right_id - ? WHERE left_id > ?', array($pageDiff, $pageDiff, $items[0]['right_id']));
                        $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET right_id = right_id + ? WHERE left_id <= ? AND right_id >= ?', array($pageDiff, $newParent['left_id'], $newParent['right_id']));
                        $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id + ?, right_id = right_id + ? WHERE left_id > ?', array($pageDiff, $pageDiff, $newParent['left_id']));
                    }

                    // Einträge aktualisieren
                    $c_items = count($items);
                    for ($i = 0; $i < $c_items; ++$i) {
                        $parent = $this->db->fetchAssoc('SELECT id FROM ' . $this->tableName . ' WHERE left_id < ? AND right_id > ? ORDER BY left_id DESC LIMIT 1', array($items[$i]['left_id'] + $diff, $items[$i]['right_id'] + $diff));
                        if ($this->enableBlocks === true) {
                            $bool = $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET block_id = ?, root_id = ?, parent_id = ?, left_id = ?, right_id = ? WHERE id = ?', array($blockId, $rootId, !empty($parent['id']) ? $parent['id'] : 0, $items[$i]['left_id'] + $diff, $items[$i]['right_id'] + $diff, $items[$i]['id']));
                        } else {
                            $bool = $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET root_id = ?, parent_id = ?, left_id = ?, right_id = ? WHERE id = ?', array($rootId, !empty($parent['id']) ? $parent['id'] : 0, $items[$i]['left_id'] + $diff, $items[$i]['right_id'] + $diff, $items[$i]['id']));
                        }
                        if ($bool === false)
                            break;
                    }
                    $this->db->update($this->tableName, $updateValues, array('id' => $id));
                    $this->db->commit();
                }
            }
            return $bool;
        } catch (\Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * Methode zum Umsortieren von Knoten
     *
     * @param integer $id
     * @param string $mode
     *
     * @return boolean
     */
    public function order($id, $mode)
    {
        if ($this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->tableName . ' WHERE id = ?', array($id)) == 1) {
            $items = $this->db->fetchAll('SELECT c.id, ' . ($this->enableBlocks === true ? 'c.block_id, ' : '') . 'c.left_id, c.right_id FROM ' . $this->tableName . ' AS p, ' . $this->tableName . ' AS c WHERE p.id = ? AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC', array($id), array(\PDO::PARAM_INT));

            if ($mode === 'up' && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->tableName . ' WHERE right_id = ?' . ($this->enableBlocks === true ? ' AND block_id = ' . $items[0]['block_id'] : ''), array($items[0]['left_id'] - 1)) > 0) {
                // Vorherigen Knoten mit allen Kindern selektieren
                $elem = $this->db->fetchAll('SELECT c.id, c.left_id, c.right_id FROM ' . $this->tableName . ' AS p, ' . $this->tableName . ' AS c WHERE p.right_id = ? AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC', array($items[0]['left_id'] - 1));
                $diffLeft = $items[0]['left_id'] - $elem[0]['left_id'];
                $diffRight = $items[0]['right_id'] - $elem[0]['right_id'];
            } elseif ($mode === 'down' && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->tableName . ' WHERE left_id = ?' . ($this->enableBlocks === true ? ' AND block_id = ' . $items[0]['block_id'] : ''), array($items[0]['right_id'] + 1)) > 0) {
                // Nachfolgenden Knoten mit allen Kindern selektieren
                $elem = $this->db->fetchAll('SELECT c.id, c.left_id, c.right_id FROM ' . $this->tableName . ' AS p, ' . $this->tableName . ' AS c WHERE p.left_id = ? AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC', array($items[0]['right_id'] + 1));
                $diffLeft = $elem[0]['left_id'] - $items[0]['left_id'];
                $diffRight = $elem[0]['right_id'] - $items[0]['right_id'];
            } else {
                return false;
            }

            $c_elem = count($elem);
            $c_items = count($items);
            $elemIds = $itemsIds = [];

            for ($i = 0; $i < $c_elem; ++$i) {
                $elemIds[] = $elem[$i]['id'];
            }
            for ($i = 0; $i < $c_items; ++$i) {
                $itemsIds[] = $items[$i]['id'];
            }

            $this->db->beginTransaction();
            try {
                if ($mode === 'up') {
                    $bool = $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id + ?, right_id = right_id + ? WHERE id IN(?)', array($diffRight, $diffRight, $elemIds), array(\PDO::PARAM_INT, \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY));
                    $bool2 = $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id - ?, right_id = right_id - ? WHERE id IN(?)', array($diffLeft, $diffLeft, $itemsIds), array(\PDO::PARAM_INT, \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY));
                } else {
                    $bool = $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id - ?, right_id = right_id - ? WHERE id IN(?)', array($diffLeft, $diffLeft, $elemIds), array(\PDO::PARAM_INT, \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY));
                    $bool2 = $this->db->executeUpdate('UPDATE ' . $this->tableName . ' SET left_id = left_id + ?, right_id = right_id + ? WHERE id IN(?)', array($diffRight, $diffRight, $itemsIds), array(\PDO::PARAM_INT, \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY));
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