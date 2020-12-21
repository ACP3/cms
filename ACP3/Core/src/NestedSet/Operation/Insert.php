<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\NestedSet\Operation;

class Insert extends AbstractOperation
{
    /**
     * @return int|false
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(array $insertValues, int $parentId = 0)
    {
        // No parent item has been assigned
        if ($this->nestedSetRepository->nodeExists($parentId) === false) {
            // Select the last result set
            $maxRightId = $this->fetchMaximumRightId($insertValues[$this->nestedSetRepository::BLOCK_COLUMN_NAME]);

            $insertValues['root_id'] = 0;
            $insertValues['left_id'] = $maxRightId + 1;
            $insertValues['right_id'] = $maxRightId + 2;

            $this->adjustFollowingNodesAfterInsert(2, $insertValues['left_id']);

            $this->db->getConnection()->insert($this->nestedSetRepository->getTableName(), $insertValues);
            $lastInsertId = (int) $this->db->getConnection()->lastInsertId();
            $this->db->getConnection()->update(
                $this->nestedSetRepository->getTableName(),
                ['root_id' => $lastInsertId],
                ['id' => $lastInsertId]
            );
        } else { // a parent item for the node has been assigned
            $parent = $this->nestedSetRepository->fetchNodeById($parentId);

            $this->adjustFollowingNodesAfterInsert(2, $parent['right_id']);
            $this->adjustParentNodesAfterInsert(2, $parent['left_id'], $parent['right_id']);

            $insertValues['root_id'] = $parent['root_id'];
            $insertValues['left_id'] = $parent['right_id'];
            $insertValues['right_id'] = $parent['right_id'] + 1;

            $this->db->getConnection()->insert($this->nestedSetRepository->getTableName(), $insertValues);

            $lastInsertId = (int) $this->db->getConnection()->lastInsertId();
        }

        return $lastInsertId;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function fetchMaximumRightId(int $blockId): int
    {
        if ($this->isBlockAware() === true) {
            $maxRightId = $this->nestedSetRepository->fetchMaximumRightIdByBlockId($blockId);
        }
        if (empty($maxRightId)) {
            $maxRightId = $this->nestedSetRepository->fetchMaximumRightId();
        }

        return $maxRightId;
    }
}
