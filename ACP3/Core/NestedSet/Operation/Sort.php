<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\NestedSet\Operation;

/**
 * Class Sort
 * @package ACP3\Core\NestedSet\Operation
 */
class Sort extends AbstractOperation
{
    /**
     * @param int    $resultId
     * @param string $mode
     *
     * @return bool
     */
    public function execute($resultId, $mode)
    {
        if ($this->nestedSetRepository->nodeExists($resultId) === true) {
            $nodes = $this->nestedSetRepository->fetchNodeWithSiblings($resultId);

            if ($mode === 'up' &&
                $this->nestedSetRepository->nextNodeExists(
                    $nodes[0]['left_id'] - 1, $this->getBlockId($nodes[0])
                ) === true
            ) {
                return $this->sortUp($nodes);
            } elseif ($mode === 'down' &&
                $this->nestedSetRepository->previousNodeExists(
                    $nodes[0]['right_id'] + 1, $this->getBlockId($nodes[0])
                ) === true
            ) {
                return $this->sortDown($nodes);
            }
        }

        return false;
    }

    /**
     * @param array $nodes
     *
     * @return bool
     * @throws \Doctrine\DBAL\ConnectionException
     */
    protected function sortUp(array $nodes)
    {
        $callback = function () use ($nodes) {
            $prevNodes = $this->nestedSetRepository->fetchPrevNodeWithSiblings($nodes[0]['left_id'] - 1);

            list($diffLeft, $diffRight) = $this->calcDiffBetweenNodes($nodes[0], $prevNodes[0]);

            return $this->updateNodesDown($diffRight, $prevNodes) && $this->moveNodesUp($diffLeft, $nodes);
        };

        return $this->db->executeTransactionalQuery($callback);
    }

    /**
     * @param array $nodes
     *
     * @return bool
     * @throws \Doctrine\DBAL\ConnectionException
     */
    protected function sortDown(array $nodes)
    {
        $callback = function () use ($nodes) {
            $nextNodes = $this->nestedSetRepository->fetchNextNodeWithSiblings($nodes[0]['right_id'] + 1);

            list($diffLeft, $diffRight) = $this->calcDiffBetweenNodes($nextNodes[0], $nodes[0]);

            return $this->moveNodesUp($diffLeft, $nextNodes) && $this->updateNodesDown($diffRight, $nodes);
        };

        return $this->db->executeTransactionalQuery($callback);
    }

    /**
     * @param array $nodes
     *
     * @return array
     */
    protected function fetchAffectedNodesForReorder(array $nodes)
    {
        $rtn = [];
        foreach ($nodes as $node) {
            $rtn[] = $node['id'];
        }

        return $rtn;
    }

    /**
     * @param int   $diff
     * @param array $nodes
     *
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function updateNodesDown($diff, array $nodes)
    {
        return $this->db->getConnection()->executeUpdate(
            "UPDATE {$this->nestedSetRepository->getTableName()} SET left_id = left_id + ?, right_id = right_id + ? WHERE id IN(?)",
            [$diff, $diff, $this->fetchAffectedNodesForReorder($nodes)],
            [\PDO::PARAM_INT, \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY]
        );
    }

    /**
     * @param int   $diff
     * @param array $nodes
     *
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function moveNodesUp($diff, array $nodes)
    {
        return $this->db->getConnection()->executeUpdate(
            "UPDATE {$this->nestedSetRepository->getTableName()} SET left_id = left_id - ?, right_id = right_id - ? WHERE id IN(?)",
            [$diff, $diff, $this->fetchAffectedNodesForReorder($nodes)],
            [\PDO::PARAM_INT, \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY]
        );
    }

    /**
     * @param array $node
     * @param array $elem
     *
     * @return array
     */
    protected function calcDiffBetweenNodes(array $node, array $elem)
    {
        return [
            $node['left_id'] - $elem['left_id'],
            $node['right_id'] - $elem['right_id']
        ];
    }

    /**
     * @param array $node
     *
     * @return string
     */
    protected function getBlockId(array $node)
    {
        return ($this->isBlockAware === true ? $node['block_id'] : 0);
    }
}
