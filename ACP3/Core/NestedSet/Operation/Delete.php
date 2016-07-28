<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\NestedSet\Operation;

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
            $nodes = $this->nestedSetRepository->fetchNodeWithSiblings($this->tableName, (int)$resultId);
            if (!empty($nodes)) {
                $this->db->getConnection()->delete($this->tableName, ['id' => (int)$resultId]);

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
        foreach ($nodes as $node) {
            $rootId = $this->nestedSetRepository->fetchRootNode($this->tableName, $node['left_id'], $node['right_id']);
            $parentId = $this->nestedSetRepository->fetchParentNode($this->tableName, $node['left_id'], $node['right_id']);

            // root_id und parent_id der Kinder aktualisieren
            $this->db->getConnection()->executeUpdate(
                "UPDATE {$this->tableName} SET root_id = ?, parent_id = ?, left_id = left_id - 1, right_id = right_id - 1 WHERE id = ?",
                [
                    !empty($rootId) ? $rootId : $node['id'],
                    $parentId,
                    $node['id']
                ]
            );
        }
    }
}
