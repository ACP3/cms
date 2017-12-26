<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\NestedSet\Operation;

use Doctrine\DBAL\Connection;

/**
 * Class Delete
 * @package ACP3\Core\NestedSet\Operation
 */
class Delete extends AbstractOperation
{
    /**
     * @param int $resultId
     *
     * @return bool
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function execute($resultId)
    {
        $callback = function () use ($resultId) {
            $nodes = $this->nestedSetRepository->fetchNodeWithSiblings((int)$resultId);
            if (!empty($nodes)) {
                $this->db->getConnection()->delete(
                    $this->nestedSetRepository->getTableName(),
                    ['id' => (int)$resultId]
                );

                $this->moveSiblingsOneLevelUp($nodes);
                $this->adjustParentNodesAfterSeparation(2, $nodes[0]['left_id'], $nodes[0]['right_id']);
                $this->adjustFollowingNodesAfterSeparation(2, $nodes[0]['right_id']);

                return true;
            }

            return false;
        };

        return $this->db->executeTransactionalQuery($callback);
    }

    /**
     * @param array $nodes
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function moveSiblingsOneLevelUp(array $nodes)
    {
        array_shift($nodes);

        // Update the root_id and parent_id of the siblings
        foreach ($nodes as $node) {
            $parentId = $this->nestedSetRepository->fetchParentNode($node['left_id'], $node['right_id']);

            $this->db->getConnection()->executeUpdate(
                "UPDATE {$this->nestedSetRepository->getTableName()} SET root_id = ?, parent_id = ? WHERE id = ?",
                [
                    $nodes[0]['id'],
                    $parentId,
                    $node['id']
                ]
            );
        }

        $this->db->getConnection()->executeUpdate(
            "UPDATE {$this->nestedSetRepository->getTableName()} SET left_id = left_id - 1, right_id = right_id - 1 WHERE id IN(?)",
            [$this->getNodeIds($nodes)],
            [Connection::PARAM_INT_ARRAY]
        );
    }

    /**
     * @param array $nodes
     * @return array
     */
    private function getNodeIds(array $nodes)
    {
        $nodeIds = [];
        foreach ($nodes as $node) {
            $nodeIds[] = $node['id'];
        }
        return $nodeIds;
    }
}
