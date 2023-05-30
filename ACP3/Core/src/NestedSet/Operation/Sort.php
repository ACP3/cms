<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\NestedSet\Operation;

class Sort extends AbstractOperation
{
    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function execute(int $resultId, string $mode): bool
    {
        if ($this->nestedSetRepository->nodeExists($resultId) === true) {
            $nodes = $this->nestedSetRepository->fetchNodeWithSiblings($resultId);

            if ($mode === 'up'
                && $this->nestedSetRepository->nextNodeExists($nodes[0]['left_id'] - 1, $this->getBlockId($nodes[0])) === true) {
                return $this->sortUp($nodes);
            }

            if ($mode === 'down'
                && $this->nestedSetRepository->previousNodeExists(
                    $nodes[0]['right_id'] + 1,
                    $this->getBlockId($nodes[0])
                ) === true
            ) {
                return $this->sortDown($nodes);
            }
        }

        return false;
    }

    /**
     * @param array<array<string, mixed>> $nodes
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function sortUp(array $nodes): bool
    {
        $prevNodes = $this->nestedSetRepository->fetchPrevNodeWithSiblings($nodes[0]['left_id'] - 1);

        [$diffLeft, $diffRight] = $this->calcDiffBetweenNodes($nodes[0], $prevNodes[0]);

        return $this->updateNodesDown($diffRight, $prevNodes) && $this->moveNodesUp($diffLeft, $nodes);
    }

    /**
     * @param array<array<string, mixed>> $nodes
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function sortDown(array $nodes): bool
    {
        $nextNodes = $this->nestedSetRepository->fetchNextNodeWithSiblings($nodes[0]['right_id'] + 1);

        [$diffLeft, $diffRight] = $this->calcDiffBetweenNodes($nextNodes[0], $nodes[0]);

        return $this->moveNodesUp($diffLeft, $nextNodes) && $this->updateNodesDown($diffRight, $nodes);
    }

    /**
     * @param array<array<string, mixed>> $nodes
     *
     * @return int[]
     */
    protected function fetchAffectedNodesForReorder(array $nodes): array
    {
        return array_map(static fn ($node) => (int) $node['id'], $nodes);
    }

    /**
     * @param array<array<string, mixed>> $nodes
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function updateNodesDown(int $diff, array $nodes): bool
    {
        return $this->nestedSetRepository->moveNodesWithinTree($diff, $diff, $this->fetchAffectedNodesForReorder($nodes));
    }

    /**
     * @param array<array<string, mixed>> $nodes
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function moveNodesUp(int $diff, array $nodes): bool
    {
        return $this->nestedSetRepository->moveNodesWithinTree($diff * -1, $diff * -1, $this->fetchAffectedNodesForReorder($nodes));
    }

    /**
     * @param array<string, mixed> $node
     * @param array<string, mixed> $elem
     *
     * @return array{int, int}
     */
    protected function calcDiffBetweenNodes(array $node, array $elem): array
    {
        return [
            $node['left_id'] - $elem['left_id'],
            $node['right_id'] - $elem['right_id'],
        ];
    }

    /**
     * @param array<string, mixed> $node
     */
    protected function getBlockId(array $node): int
    {
        return $this->isBlockAware() === true ? $node[$this->nestedSetRepository::BLOCK_COLUMN_NAME] : 0;
    }
}
