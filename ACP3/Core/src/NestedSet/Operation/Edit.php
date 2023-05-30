<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\NestedSet\Operation;

class Edit extends AbstractOperation
{
    /**
     * Methode zum Bearbeiten eines Knotens.
     *
     * @param array<string, mixed> $updateValues
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function execute(int $resultId, int $parentId, int $blockId, array $updateValues): bool
    {
        $nodes = $this->nestedSetRepository->fetchNodeWithSiblings($resultId);

        // Überprüfen, ob Seite ein Root-Element ist und ob dies auch so bleiben soll
        if ($this->nodeIsRootItemAndNoChangeNeed($parentId, $blockId, $nodes[0])) {
            $result = $this->nestedSetRepository->update($updateValues, $resultId);
        } else {
            $currentParent = $this->nestedSetRepository->fetchParentNode(
                $nodes[0]['left_id'],
                $nodes[0]['right_id']
            );

            // Überprüfung, falls Seite kein Root-Element ist und auch keine Veränderung vorgenommen werden soll...
            if (!empty($currentParent) && $currentParent === $parentId) {
                $result = $this->nestedSetRepository->update($updateValues, $resultId);
            } else { // ...ansonsten den Baum bearbeiten...
                // Neues Elternelement
                $newParent = $this->nestedSetRepository->fetchNodeById($parentId);

                if (empty($newParent)) {
                    [$rootId, $diff] = $this->nodeBecomesRootNode($resultId, $blockId, $nodes);
                } else {
                    [$diff, $rootId] = $this->moveNodeToNewParent($newParent, $nodes);
                }

                $result = $this->adjustNodeSiblings($blockId, $nodes, $diff, $rootId);

                $this->nestedSetRepository->update($updateValues, $resultId);
            }
        }

        return $result !== false;
    }

    /**
     * @param array<string, mixed> $node
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function nodeIsRootItemAndNoChangeNeed(int $parentId, int $blockId, array $node): bool
    {
        return empty($parentId)
            && ($this->isBlockAware() === false || ($this->isBlockAware() === true && $blockId === (int) $node[$this->nestedSetRepository::BLOCK_COLUMN_NAME]))
            && $this->nestedSetRepository->nodeIsRootItem($node['left_id'], $node['right_id']) === true;
    }

    /**
     * @param array<array<string, mixed>> $nodes
     *
     * @return array{int, int}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function nodeBecomesRootNode(int $id, int $blockId, array $nodes): array
    {
        $itemDiff = $this->calcDiffBetweenNodes($nodes[0]['left_id'], $nodes[0]['right_id']);
        if ($this->isBlockAware() === true) {
            if ((int) $nodes[0][$this->nestedSetRepository::BLOCK_COLUMN_NAME] !== $blockId) {
                $diff = $this->nodeBecomesRootNodeInNewBlock($blockId, $nodes, $itemDiff);
            } else {
                $diff = $this->nodeBecomesRootNodeInSameBlock($nodes[0], $itemDiff);
            }
        } else {
            $maxId = $this->nestedSetRepository->fetchMaximumRightId();
            $diff = $maxId - $nodes[0]['right_id'];

            $this->adjustParentNodesAfterSeparation($itemDiff, $nodes[0]['left_id'], $nodes[0]['right_id']);
            $this->adjustFollowingNodesAfterSeparation($itemDiff, $nodes[0]['right_id']);
        }

        return [$id, $diff];
    }

    /**
     * @param array<array<string, mixed>> $nodes
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function nodeBecomesRootNodeInNewBlock(int $blockId, array $nodes, int $itemDiff): int
    {
        $newBlockLeftId = $this->nestedSetRepository->fetchMinimumLeftIdByBlockId($blockId);

        // Falls die Knoten in einen leeren Block verschoben werden sollen,
        // die right_id des letzten Elementes verwenden
        if (empty($newBlockLeftId)) {
            $newBlockLeftId = $this->nestedSetRepository->fetchMaximumRightId();
            ++$newBlockLeftId;
        }

        if ($blockId > $nodes[0][$this->nestedSetRepository::BLOCK_COLUMN_NAME]) {
            $newBlockLeftId -= $itemDiff;
        }

        $this->adjustParentNodesAfterSeparation($itemDiff, $nodes[0]['left_id'], $nodes[0]['right_id']);
        $this->adjustFollowingNodesAfterSeparation($itemDiff, $nodes[0]['right_id']);
        $this->adjustFollowingNodesAfterInsert($itemDiff, $newBlockLeftId);

        return $newBlockLeftId - $nodes[0]['left_id'];
    }

    /**
     * @param array<string, mixed> $node
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function nodeBecomesRootNodeInSameBlock(array $node, int $itemDiff): int
    {
        $maxId = $this->nestedSetRepository->fetchMaximumRightIdByBlockId($node[$this->nestedSetRepository::BLOCK_COLUMN_NAME]);

        $this->adjustParentNodesAfterSeparation($itemDiff, $node['left_id'], $node['right_id']);

        $this->nestedSetRepository->moveSubsequentNodesOfBlock($itemDiff, $node['right_id'], $node[$this->nestedSetRepository::BLOCK_COLUMN_NAME]);

        return $maxId - $node['right_id'];
    }

    /**
     * @param array<array<string, mixed>> $nodes
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function adjustNodeSiblings(int $blockId, array $nodes, int $diff, int $rootId): bool
    {
        foreach ($nodes as $node) {
            $node['left_id'] += $diff;
            $node['right_id'] += $diff;

            $parentId = $this->nestedSetRepository->fetchParentNode(
                $node['left_id'],
                $node['right_id']
            );
            if ($this->isBlockAware() === true) {
                $this->nestedSetRepository->update([
                    $this->nestedSetRepository::BLOCK_COLUMN_NAME => $blockId,
                    'root_id' => $rootId,
                    'parent_id' => $parentId,
                    'left_id' => $node['left_id'],
                    'right_id' => $node['right_id'],
                ], $node['id']);
            } else {
                $this->nestedSetRepository->update([
                    'root_id' => $rootId,
                    'parent_id' => $parentId,
                    'left_id' => $node['left_id'],
                    'right_id' => $node['right_id'],
                ], $node['id']);
            }
        }

        return true;
    }

    protected function calcDiffBetweenNodes(int $leftId, int $rightId): int
    {
        return $rightId - $leftId + 1;
    }

    /**
     * @param array<string, mixed>        $newParent
     * @param array<array<string, mixed>> $nodes
     *
     * @return array{int, int}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function moveNodeToNewParent(array $newParent, array $nodes): array
    {
        $itemDiff = $this->calcDiffBetweenNodes($nodes[0]['left_id'], $nodes[0]['right_id']);

        // Teilbaum nach unten...
        if ($newParent['left_id'] > $nodes[0]['left_id']) {
            $newParent['left_id'] -= $itemDiff;
            $newParent['right_id'] -= $itemDiff;
        }

        $diff = $newParent['left_id'] - $nodes[0]['left_id'] + 1;
        $rootId = $newParent['root_id'];

        $this->adjustParentNodesAfterSeparation($itemDiff, $nodes[0]['left_id'], $nodes[0]['right_id']);
        $this->adjustFollowingNodesAfterSeparation($itemDiff, $nodes[0]['right_id']);
        $this->adjustParentNodesAfterInsert($itemDiff, $newParent['left_id'], $newParent['right_id']);
        $this->adjustFollowingNodesAfterInsert($itemDiff, $newParent['left_id'] + 1);

        return [$diff, $rootId];
    }
}
