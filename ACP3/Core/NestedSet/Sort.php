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
        if ($this->nestedSetModel->nodeExists($this->tableName, $id) === true) {
            $nodes = $this->nestedSetModel->fetchNodeWithSiblings($this->tableName, $id, $this->enableBlocks);

            if ($mode === 'up' &&
                $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->tableName} WHERE right_id = ?{$this->addBlockIdToWhereClause($nodes)}", [$nodes[0]['left_id'] - 1]) > 0) {
                return $this->sortNodeUpwards($nodes);
            } elseif ($mode === 'down' &&
                $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->tableName} WHERE left_id = ?{$this->addBlockIdToWhereClause($nodes)}", [$nodes[0]['right_id'] + 1]) > 0) {
                return $this->sortNodeDownwards($nodes);
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
    protected function sortNodeUpwards(array $nodes)
    {
        // Vorherigen Knoten mit allen Kindern selektieren
        $prevNodes = $this->db->fetchAll("SELECT c.id, c.left_id, c.right_id FROM {$this->tableName} AS p, {$this->tableName} AS c WHERE p.right_id = ? AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC", [$nodes[0]['left_id'] - 1]);

        $callback = function () use ($prevNodes, $nodes) {
            list($diffLeft, $diffRight) = $this->calcDiffBetweenNodes($nodes[0], $prevNodes[0]);

            $bool = $this->db->getConnection()->executeUpdate(
                "UPDATE {$this->tableName} SET left_id = left_id + ?, right_id = right_id + ? WHERE id IN(?)",
                [$diffRight, $diffRight, $this->fetchAffectedNodesForReorder($prevNodes)],
                [\PDO::PARAM_INT, \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY]
            );
            $bool2 = $this->db->getConnection()->executeUpdate(
                "UPDATE {$this->tableName} SET left_id = left_id - ?, right_id = right_id - ? WHERE id IN(?)",
                [$diffLeft, $diffLeft, $this->fetchAffectedNodesForReorder($nodes)],
                [\PDO::PARAM_INT, \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY]
            );
            return $bool && $bool2;
        };

        return $this->db->executeTransactionalQuery($callback);
    }

    /**
     * @param array $nodes
     *
     * @return bool
     * @throws \Doctrine\DBAL\ConnectionException
     */
    protected function sortNodeDownwards(array $nodes)
    {
        // Nachfolgenden Knoten mit allen Kindern selektieren
        $nextNodes = $this->db->fetchAll(
            "SELECT c.id, c.left_id, c.right_id FROM {$this->tableName} AS p, {$this->tableName} AS c WHERE p.left_id = ? AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC",
            [$nodes[0]['right_id'] + 1]
        );

        $callback = function () use ($nextNodes, $nodes) {
            list($diffLeft, $diffRight) = $this->calcDiffBetweenNodes($nextNodes[0], $nodes[0]);

            $bool = $this->db->getConnection()->executeUpdate(
                "UPDATE {$this->tableName} SET left_id = left_id - ?, right_id = right_id - ? WHERE id IN(?)",
                [$diffLeft, $diffLeft, $this->fetchAffectedNodesForReorder($nextNodes)],
                [\PDO::PARAM_INT, \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY]
            );
            $bool2 = $this->db->getConnection()->executeUpdate(
                "UPDATE {$this->tableName} SET left_id = left_id + ?, right_id = right_id + ? WHERE id IN(?)",
                [$diffRight, $diffRight, $this->fetchAffectedNodesForReorder($nodes)],
                [\PDO::PARAM_INT, \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY]
            );
            return $bool && $bool2;
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
     * @param array $nodes
     *
     * @return string
     */
    protected function addBlockIdToWhereClause(array $nodes)
    {
        return ($this->enableBlocks === true ? ' AND block_id = ' . $nodes[0]['block_id'] : '');
    }
}