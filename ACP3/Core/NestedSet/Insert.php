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
     * @return int|bool
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function execute(array $insertValues, $parentId = 0)
    {
        $callback = function() use ($insertValues, $parentId) {
            // No parent item has been assigned
            if ($this->nestedSetModel->nodeExists($this->tableName, (int) $parentId) === false) {
                // Select the last result set
                $maxRightId = $this->fetchMaximumRightId($insertValues['block_id']);

                $insertValues['left_id'] = $maxRightId + 1;
                $insertValues['right_id'] = $maxRightId + 2;

                $this->adjustFollowingNodesAfterInsert(2, $insertValues['left_id']);

                $this->db->getConnection()->insert($this->tableName, $insertValues);
                $rootId = $this->db->getConnection()->lastInsertId();
                $result = $this->db->getConnection()->update($this->tableName, ['root_id' => $rootId], ['id' => $rootId]);
            } else { // a parent item for the node has been assigned
                $parent = $this->nestedSetModel->fetchNodeById($this->tableName, (int) $parentId);

                $this->adjustFollowingNodesAfterInsert(2, $parent['right_id']);
                $this->adjustParentNodesAfterInsert(2, $parent['left_id'], $parent['right_id']);

                $insertValues['root_id'] = $parent['root_id'];
                $insertValues['left_id'] = $parent['right_id'];
                $insertValues['right_id'] = $parent['right_id'] + 1;

                $this->db->getConnection()->insert($this->tableName, $insertValues);

                $result = (int) $this->db->getConnection()->lastInsertId();
            }

            return $result;
        };

        return $this->db->executeTransactionalQuery($callback);
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