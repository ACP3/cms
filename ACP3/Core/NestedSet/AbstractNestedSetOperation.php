<?php
namespace ACP3\Core\NestedSet;

use ACP3\Core\Database\Connection;

/**
 * Class AbstractNestedSetOperation
 * @package ACP3\Core\NestedSet
 */
abstract class AbstractNestedSetOperation
{
    /**
     * @var \ACP3\Core\Database\Connection
     */
    protected $db;
    /**
     * @var \ACP3\Core\NestedSet\NestedSetRepository
     */
    protected $nestedSetRepository;

    /**
     * @var bool
     */
    protected $enableBlocks;
    /**
     * @var string
     */
    protected $tableName;

    /**
     * @param \ACP3\Core\Database\Connection           $db
     * @param \ACP3\Core\NestedSet\NestedSetRepository $nestedSetRepository
     */
    public function __construct(
        Connection $db,
        NestedSetRepository $nestedSetRepository
    ) {
        $this->db = $db;
        $this->nestedSetRepository = $nestedSetRepository;
    }

    /**
     * @param string $tableName
     *
     * @return $this
     */
    public function setTableName($tableName)
    {
        $this->tableName = $this->db->getPrefixedTableName($tableName);

        return $this;
    }

    /**
     * @param bool $enableBlocks
     *
     * @return $this
     */
    public function setEnableBlocks($enableBlocks)
    {
        $this->enableBlocks = $enableBlocks;

        return $this;
    }

    /**
     * @param int $diff
     * @param int $leftId
     * @param int $rightId
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function adjustParentNodesAfterSeparation($diff, $leftId, $rightId)
    {
        $this->db->getConnection()->executeUpdate(
            "UPDATE {$this->tableName} SET right_id = right_id - ? WHERE left_id < ? AND right_id > ?",
            [$diff, $leftId, $rightId]
        );
    }

    /**
     * @param int $diff
     * @param int $leftId
     * @param int $rightId
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function adjustParentNodesAfterInsert($diff, $leftId, $rightId)
    {
        $this->db->getConnection()->executeUpdate(
            "UPDATE {$this->tableName} SET right_id = right_id + ? WHERE left_id <= ? AND right_id >= ?",
            [$diff, $leftId, $rightId]
        );
    }

    /**
     * @param int $diff
     * @param int $leftId
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function adjustFollowingNodesAfterSeparation($diff, $leftId)
    {
        $this->db->getConnection()->executeUpdate(
            "UPDATE {$this->tableName} SET left_id = left_id - ?, right_id = right_id - ? WHERE left_id > ?",
            [$diff, $diff, $leftId]
        );
    }

    /**
     * @param int $diff
     * @param int $leftId
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function adjustFollowingNodesAfterInsert($diff, $leftId)
    {
        $this->db->getConnection()->executeUpdate(
            "UPDATE {$this->tableName} SET left_id = left_id + ?, right_id = right_id + ? WHERE left_id >= ?",
            [$diff, $diff, $leftId]
        );
    }
}
