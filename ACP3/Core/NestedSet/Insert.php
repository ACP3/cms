<?php
namespace ACP3\Core\NestedSet;

/**
 * Class Insert
 * @package ACP3\Core\NestedSet
 */
class Insert extends AbstractNestedSetOperation
{
    /**
     * @param array $insertValues
     * @param int   $parentId
     *
     * @return bool
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function execute(array $insertValues, $parentId = 0)
    {
        $parentId = (int)$parentId;

        $this->db->getConnection()->beginTransaction();
        try {
            // No parent item has been assigned
            if (empty($parentId) ||
                $this->nestedSetModel->nodeExists($this->tableName, $parentId) === false
            ) {
                // Select the last result set
                $maxRightId = $this->fetchMaximumRightId($insertValues['block_id']);

                $insertValues['left_id'] = $maxRightId + 1;
                $insertValues['right_id'] = $maxRightId + 2;

                $this->adjustFollowingNodes($insertValues['left_id']);

                $this->db->getConnection()->insert($this->tableName, $insertValues);
                $rootId = $this->db->getConnection()->lastInsertId();
                $this->db->getConnection()->update($this->tableName, ['root_id' => $rootId], ['id' => $rootId]);
            } else { // a parent item for the node has been assigned
                $parent = $this->nestedSetModel->fetchNodeById($this->tableName, $parentId);

                $this->adjustFollowingNodes($parent['right_id']);

                // Adjust parent nodes
                $this->db->getConnection()->executeUpdate(
                    "UPDATE {$this->tableName} SET right_id = right_id + 2 WHERE root_id = ? AND left_id <= ? AND right_id >= ?",
                    [$parent['root_id'], $parent['left_id'], $parent['right_id']]
                );

                $insertValues['root_id'] = $parent['root_id'];
                $insertValues['left_id'] = $parent['right_id'];
                $insertValues['right_id'] = $parent['right_id'] + 1;

                $this->db->getConnection()->insert($this->tableName, $insertValues);
            }

            $this->db->getConnection()->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->getConnection()->rollback();
        }

        return false;
    }

    /**
     * @param int $itemLeftId
     *
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function adjustFollowingNodes($itemLeftId)
    {
        return $this->db->getConnection()->executeUpdate("UPDATE {$this->tableName} SET left_id = left_id + 2, right_id = right_id + 2 WHERE left_id >= ?", [$itemLeftId]);
    }

    /**
     * @param int $blockId
     *
     * @return int
     */
    protected function fetchMaximumRightId($blockId)
    {
        if ($this->enableBlocks === true) {
            $maxRightId = $this->nestedSetModel->fetchMaximumRightIdByBlockId($this->tableName,$blockId);
        }
        if (empty($maxRightId)) {
            $maxRightId = $this->nestedSetModel->fetchMaximumRightId($this->tableName);
        }

        return (int) $maxRightId;
    }
}