<?php
namespace ACP3\Core\NestedSet;

/**
 * Class Edit
 * @package ACP3\Core\NestedSet
 */
class Edit extends AbstractNestedSetOperation
{
    /**
     * Methode zum Bearbeiten eines Knotens
     *
     * @param integer $id
     *    ID des zu bearbeitenden Knotens
     * @param integer $parentId
     *    ID des neuen Elternelements
     * @param integer $blockId
     *    ID des neuen Blocks
     * @param array   $updateValues
     *
     * @return boolean
     */
    public function execute($id, $parentId, $blockId, array $updateValues)
    {
        $this->db->getConnection()->beginTransaction();
        try {
            $items = $this->nestedSetModel->fetchNodeWithSiblings($this->tableName, $id, $this->enableBlocks);

            // Überprüfen, ob Seite ein Root-Element ist und ob dies auch so bleiben soll
            if ($this->isNodeRootItem($parentId, $blockId, $items[0])) {
                $bool = $this->db->getConnection()->update($this->tableName, $updateValues, ['id' => $id]);
            } else {
                $currentParent = $this->nestedSetModel->fetchParentNode(
                    $this->tableName,
                    $items[0]['left_id'],
                    $items[0]['right_id']
                );

                // Überprüfung, falls Seite kein Root-Element ist und auch keine Veränderung vorgenommen werden soll...
                if (!empty($currentParent) && $currentParent == $parentId) {
                    $bool = $this->db->getConnection()->update($this->tableName, $updateValues, ['id' => $id]);
                } else { // ...ansonsten den Baum bearbeiten...
                    $bool = false;
                    // Differenz zwischen linken und rechten Wert bilden
                    $itemDiff = $items[0]['right_id'] - $items[0]['left_id'] + 1;

                    // Neues Elternelement
                    $newParent = $this->nestedSetModel->fetchNodeById($this->tableName, $parentId);

                    // Knoten werden eigenes Root-Element
                    if (empty($newParent)) {
                        list($rootId, $diff) = $this->nodeBecomesRootNode($id, $blockId, $items, $itemDiff);
                    } else { // Knoten werden Kinder von einem anderen Knoten
                        // Teilbaum nach unten...
                        if ($newParent['left_id'] > $items[0]['left_id']) {
                            $newParent['left_id'] -= $itemDiff;
                            $newParent['right_id'] -= $itemDiff;
                        }

                        $diff = $newParent['left_id'] - $items[0]['left_id'] + 1;
                        $rootId = $newParent['root_id'];

                        $this->db->getConnection()->executeUpdate(
                            "UPDATE {$this->tableName} SET right_id = right_id - ? WHERE left_id < ? AND right_id > ?",
                            [$itemDiff, $items[0]['left_id'], $items[0]['right_id']]
                        );
                        $this->db->getConnection()->executeUpdate(
                            "UPDATE {$this->tableName} SET left_id = left_id - ?, right_id = right_id - ? WHERE left_id > ?",
                            [$itemDiff, $itemDiff, $items[0]['right_id']]
                        );
                        $this->db->getConnection()->executeUpdate(
                            "UPDATE {$this->tableName} SET right_id = right_id + ? WHERE left_id <= ? AND right_id >= ?",
                            [$itemDiff, $newParent['left_id'], $newParent['right_id']]
                        );
                        $this->db->getConnection()->executeUpdate(
                            "UPDATE {$this->tableName} SET left_id = left_id + ?, right_id = right_id + ? WHERE left_id > ?",
                            [$itemDiff, $itemDiff, $newParent['left_id']]
                        );
                    }

                    // Einträge aktualisieren
                    foreach ($items as $item) {
                        $item['left_id'] += $diff;
                        $item['right_id'] += $diff;

                        $parentId = $this->nestedSetModel->fetchParentNode(
                            $this->tableName,
                            $item['left_id'],
                            $item['right_id']
                        );
                        if ($this->enableBlocks === true) {
                            $bool = $this->db->getConnection()->executeUpdate(
                                "UPDATE {$this->tableName} SET block_id = ?, root_id = ?, parent_id = ?, left_id = ?, right_id = ? WHERE id = ?",
                                [
                                    $blockId,
                                    $rootId,
                                    $parentId,
                                    $item['left_id'],
                                    $item['right_id'],
                                    $item['id']
                                ]
                            );
                        } else {
                            $bool = $this->db->getConnection()->executeUpdate(
                                "UPDATE {$this->tableName} SET root_id = ?, parent_id = ?, left_id = ?, right_id = ? WHERE id = ?",
                                [
                                    $rootId,
                                    $parentId,
                                    $item['left_id'],
                                    $item['right_id'],
                                    $item['id']
                                ]
                            );
                        }
                        if ($bool === false) {
                            break;
                        }
                    }

                    $this->db->getConnection()->update($this->tableName, $updateValues, ['id' => $id]);
                    $this->db->getConnection()->commit();
                }
            }
            return $bool;
        } catch (\Exception $e) {
            $this->db->getConnection()->rollback();
            return false;
        }
    }

    /**
     * @param int   $parentId
     * @param int   $blockId
     * @param array $items
     *
     * @return bool
     */
    protected function isNodeRootItem($parentId, $blockId, array $items)
    {
        return empty($parentId) &&
        ($this->enableBlocks === false ||($this->enableBlocks === true && $blockId == $items['block_id'])) &&
        $this->nestedSetModel->nodeIsRootItem($this->tableName, $items['left_id'], $items['right_id']) === true;
    }

    /**
     * @param int   $id
     * @param int   $blockId
     * @param array $items
     * @param int   $itemDiff
     *
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function nodeBecomesRootNode($id, $blockId, array $items, $itemDiff)
    {
        $rootId = $id;
        if ($this->enableBlocks === true) {
            // Knoten in anderen Block verschieben
            if ($items[0]['block_id'] != $blockId) {
                $diff = $this->nodeBecomesRootNodeInNewBlock($blockId, $items, $itemDiff);
            } else { // Element zum neuen Wurzelknoten machen
                $diff = $this->nodeBecomesRootNodeInSameBlock($items, $itemDiff);
            }
        } else {
            $maxId = $this->nestedSetModel->fetchMaximumRightId($this->tableName);
            $diff = $maxId - $items[0]['right_id'];

            $this->db->getConnection()->executeUpdate(
                "UPDATE {$this->tableName} SET right_id = right_id - ? WHERE left_id < ? AND right_id > ?",
                [$itemDiff, $items[0]['left_id'], $items[0]['right_id']]
            );
            $this->db->getConnection()->executeUpdate(
                "UPDATE {$this->tableName} SET left_id = left_id - ?, right_id = right_id - ? WHERE left_id > ?",
                [$itemDiff, $itemDiff, $items[0]['right_id']]
            );
        }

        return [$rootId, $diff];
    }

    /**
     * @param int   $blockId
     * @param array $items
     * @param int   $itemDiff
     *
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function nodeBecomesRootNodeInNewBlock($blockId, array $items, $itemDiff)
    {
        $newBlockLeftId = $this->db->fetchColumn(
            "SELECT MIN(`left_id`) AS left_id FROM {$this->tableName} WHERE block_id = ?",
            [$blockId]
        );
        // Falls die Knoten in einen leeren Block verschoben werden sollen,
        // die right_id des letzten Elementes verwenden
        if (empty($newBlockLeftId) || is_null($newBlockLeftId) === true) {
            $newBlockLeftId = $this->nestedSetModel->fetchMaximumRightId($this->tableName);
            $newBlockLeftId += 1;
        }

        if ($blockId > $items[0]['block_id']) {
            $newBlockLeftId -= $itemDiff;
        }

        $this->db->getConnection()->executeUpdate(
            "UPDATE {$this->tableName} SET right_id = right_id - ? WHERE left_id < ? AND right_id > ?",
            [$itemDiff, $items[0]['left_id'], $items[0]['right_id']]
        );
        $this->db->getConnection()->executeUpdate(
            "UPDATE {$this->tableName} SET left_id = left_id - ?, right_id = right_id - ? WHERE left_id > ?",
            [$itemDiff, $itemDiff, $items[0]['right_id']]
        );
        $this->db->getConnection()->executeUpdate(
            "UPDATE {$this->tableName} SET left_id = left_id + ?, right_id = right_id + ? WHERE left_id >= ?",
            [$itemDiff, $itemDiff, $newBlockLeftId]
        );

        return $newBlockLeftId - $items[0]['left_id'];
    }

    /**
     * @param array $items
     * @param int   $itemDiff
     *
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function nodeBecomesRootNodeInSameBlock(array $items, $itemDiff)
    {
        $maxId = $this->nestedSetModel->fetchMaximumRightIdByBlockId($this->tableName, $items[0]['block_id']);

        $this->db->getConnection()->executeUpdate(
            "UPDATE {$this->tableName} SET right_id = right_id - ? WHERE left_id < ? AND right_id > ?",
            [$itemDiff, $items[0]['left_id'], $items[0]['right_id']]
        );
        $this->db->getConnection()->executeUpdate(
            "UPDATE {$this->tableName} SET left_id = left_id - ?, right_id = right_id - ? WHERE left_id > ? AND block_id = ?",
            [$itemDiff, $itemDiff, $items[0]['right_id'], $items[0]['block_id']]
        );

        return $maxId - $items[0]['right_id'];
    }
}