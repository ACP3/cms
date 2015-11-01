<?php
namespace ACP3\Core\NestedSet;

/**
 * Class Sort
 * @package ACP3\Core\NestedSet
 */
class Sort extends AbstractNestedSetOperation
{
    /**
     * @param int    $id
     * @param string $mode
     *
     * @return bool
     */
    public function execute($id, $mode)
    {
        if ($this->nestedSetRepository->nodeExists($this->tableName, $id) === true) {
            $nodes = $this->nestedSetRepository->fetchNodeWithSiblings($this->tableName, $id);

            if ($mode === 'up' &&
                $this->nestedSetRepository->nextNodeExists(
                    $this->tableName,
                    $nodes[0]['left_id'] - 1,
                    $this->getBlockId($nodes[0])
                ) === true
            ) {
                return $this->sortUp($nodes);
            } elseif ($mode === 'down' &&
                $this->nestedSetRepository->previousNodeExists(
                    $this->tableName,
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
     * @param array $nodes
     *
     * @return bool
     * @throws \Doctrine\DBAL\ConnectionException
     */
    protected function sortUp(array $nodes)
    {
        $callback = function () use ($nodes) {
            $prevNodes = $this->nestedSetRepository->fetchPrevNodeWithSiblings($this->tableName, $nodes[0]['left_id'] - 1);

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
            $nextNodes = $this->nestedSetRepository->fetchNextNodeWithSiblings($this->tableName, $nodes[0]['right_id'] + 1);

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
            "UPDATE {$this->tableName} SET left_id = left_id + ?, right_id = right_id + ? WHERE id IN(?)",
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
            "UPDATE {$this->tableName} SET left_id = left_id - ?, right_id = right_id - ? WHERE id IN(?)",
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
        return ($this->enableBlocks === true ? $node['block_id'] : 0);
    }
}