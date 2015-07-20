<?php
namespace ACP3\Core\NestedSet;

/**
 * Class Delete
 * @package ACP3\Core\NestedSet
 */
class Delete extends AbstractNestedSetOperation
{
    /**
     * @param int $id
     *
     * @return bool
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function execute($id)
    {
        $nodes = $this->nestedSetModel->fetchNodeWithSiblings($this->tableName, (int)$id, $this->enableBlocks);
        if (!empty($nodes)) {
            $this->db->getConnection()->beginTransaction();
            try {
                $this->db->getConnection()->delete($this->tableName, ['id' => (int)$id]);

                foreach ($nodes as $node) {
                    $rootId = $this->nestedSetModel->fetchRootNode($this->tableName, $node['left_id'], $node['right_id']);
                    $parentId = $this->nestedSetModel->fetchParentNode($this->tableName, $node['left_id'], $node['right_id']);

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

                $this->adjustParentNodesAfterSeparation(2, $nodes[0]['left_id'], $nodes[0]['right_id']);
                $this->adjustFollowingNodesAfterSeparation(2, $nodes[0]['right_id']);

                $this->db->getConnection()->commit();

                return true;
            } catch (\Exception $e) {
                $this->db->getConnection()->rollback();
            }
        }
        return false;
    }
}