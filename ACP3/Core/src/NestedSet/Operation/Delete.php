<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\NestedSet\Operation;

class Delete extends AbstractOperation
{
    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function execute(int $resultId): bool
    {
        $nodes = $this->nestedSetRepository->fetchNodeWithSiblings($resultId);
        if (!empty($nodes)) {
            $this->nestedSetRepository->delete($resultId);

            $this->moveSiblingsOneLevelUp($nodes);
            $this->adjustParentNodesAfterSeparation(2, $nodes[0]['left_id'], $nodes[0]['right_id']);
            $this->adjustFollowingNodesAfterSeparation(2, $nodes[0]['right_id']);

            return true;
        }

        return false;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function moveSiblingsOneLevelUp(array $nodes): void
    {
        array_shift($nodes);

        // Update the root_id and parent_id of the siblings
        foreach ($nodes as $node) {
            $parentId = $this->nestedSetRepository->fetchParentNode($node['left_id'], $node['right_id']);

            $this->nestedSetRepository->updateRootIdAndParentIdOfNode($nodes[0]['id'], $parentId, $node['id']);
        }

        $this->nestedSetRepository->moveNodesWithinTree(-1, -1, $this->getNodeIds($nodes));
    }

    /**
     * @return int[]
     */
    private function getNodeIds(array $nodes): array
    {
        return array_map(static function ($node) {
            return (int) $node['id'];
        }, $nodes);
    }
}
