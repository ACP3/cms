<?php
namespace ACP3\Core\NestedSet;

use ACP3\Core\DB;

/**
 * Class AbstractNestedSetOperation
 * @package ACP3\Core\NestedSet
 */
abstract class AbstractNestedSetOperation
{
    /**
     * @var \ACP3\Core\DB
     */
    protected $db;
    /**
     * @var \ACP3\Core\NestedSet\Model
     */
    protected $nestedSetModel;

    /**
     * @var bool
     */
    protected $enableBlocks;
    /**
     * @var string
     */
    protected $tableName;

    /**
     * @param \ACP3\Core\DB              $db
     * @param \ACP3\Core\NestedSet\Model $nestedSetModel
     */
    public function __construct(
        DB $db,
        Model $nestedSetModel
    )
    {
        $this->db = $db;
        $this->nestedSetModel = $nestedSetModel;
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