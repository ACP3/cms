<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\NestedSet;

use ACP3\Core\NestedSet\Operation\Delete;
use ACP3\Core\NestedSet\Operation\Edit;
use ACP3\Core\NestedSet\Operation\Insert;
use ACP3\Core\NestedSet\Operation\Sort;

/**
 * Class NestedSet
 * @package ACP3\Core\NestedSet
 */
class NestedSet
{
    /**
     * @var \ACP3\Core\NestedSet\Operation\Insert
     */
    protected $insert;
    /**
     * @var \ACP3\Core\NestedSet\Operation\Edit
     */
    protected $edit;
    /**
     * @var \ACP3\Core\NestedSet\Operation\Delete
     */
    protected $delete;
    /**
     * @var \ACP3\Core\NestedSet\Operation\Sort
     */
    protected $sort;

    /**
     * @param \ACP3\Core\NestedSet\Operation\Insert $insert
     * @param \ACP3\Core\NestedSet\Operation\Edit   $edit
     * @param \ACP3\Core\NestedSet\Operation\Delete $delete
     * @param \ACP3\Core\NestedSet\Operation\Sort   $sort
     */
    public function __construct(
        Insert $insert,
        Edit $edit,
        Delete $delete,
        Sort $sort
    ) {
        $this->insert = $insert;
        $this->edit = $edit;
        $this->delete = $delete;
        $this->sort = $sort;
    }

    /**
     * Löscht einen Knoten und verschiebt seine Kinder eine Ebene nach oben
     *
     * @param integer $resultId
     * @param string  $tableName
     * @param bool    $enableBlocks
     *
     * @return bool
     */
    public function deleteNode($resultId, $tableName, $enableBlocks = false)
    {
        return $this->delete
            ->setTableName($tableName)
            ->setEnableBlocks($enableBlocks)
            ->execute($resultId);
    }

    /**
     * Erstellt einen neuen Knoten
     *
     * @param integer $parentId
     * @param array   $insertValues
     * @param string  $tableName
     * @param bool    $enableBlocks
     *
     * @return int|bool
     */
    public function insertNode($parentId, array $insertValues, $tableName, $enableBlocks = false)
    {
        return $this->insert
            ->setTableName($tableName)
            ->setEnableBlocks($enableBlocks)
            ->execute($insertValues, $parentId);
    }

    /**
     * Methode zum Bearbeiten eines Knotens
     *
     * @param integer $resultId
     * @param integer $parentId
     * @param integer $blockId
     * @param array   $updateValues
     * @param string  $tableName
     * @param bool    $enableBlocks
     *
     * @return bool
     */
    public function editNode(
        $resultId,
        $parentId,
        $blockId,
        array $updateValues,
        $tableName,
        $enableBlocks = false
    ) {
        return $this->edit
            ->setTableName($tableName)
            ->setEnableBlocks($enableBlocks)
            ->execute(
                $resultId,
                $parentId,
                $blockId,
                $updateValues
            );
    }

    /**
     * Methode zum Umsortieren von Knoten
     *
     * @param integer $resultId
     * @param string  $mode
     * @param string  $tableName
     * @param bool    $enableBlocks
     *
     * @return bool
     */
    public function sort($resultId, $mode, $tableName, $enableBlocks = false)
    {
        return $this->sort
            ->setTableName($tableName)
            ->setEnableBlocks($enableBlocks)
            ->execute($resultId, $mode);
    }
}
