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
}