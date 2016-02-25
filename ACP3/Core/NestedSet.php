<?php
namespace ACP3\Core;

use ACP3\Core\NestedSet\Delete;
use ACP3\Core\NestedSet\Edit;
use ACP3\Core\NestedSet\Insert;
use ACP3\Core\NestedSet\Sort;

/**
 * Class NestedSet
 * @package ACP3\Core
 */
class NestedSet
{
    /**
     * @var \ACP3\Core\NestedSet\Insert
     */
    protected $insert;
    /**
     * @var \ACP3\Core\NestedSet\Edit
     */
    protected $edit;
    /**
     * @var \ACP3\Core\NestedSet\Delete
     */
    protected $delete;
    /**
     * @var \ACP3\Core\NestedSet\Sort
     */
    protected $sort;

    /**
     * @param \ACP3\Core\NestedSet\Insert $insert
     * @param \ACP3\Core\NestedSet\Edit   $edit
     * @param \ACP3\Core\NestedSet\Delete $delete
     * @param \ACP3\Core\NestedSet\Sort   $sort
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
     * @param integer $id
     *  Die ID des zu löschenden Datensatzes
     * @param string  $tableName
     * @param bool    $enableBlocks
     *
     * @return bool
     */
    public function deleteNode($id, $tableName, $enableBlocks = false)
    {
        return $this->delete
            ->setTableName($tableName)
            ->setEnableBlocks($enableBlocks)
            ->execute($id);
    }

    /**
     * Erstellt einen neuen Knoten
     *
     * @param integer $parentId
     *    ID der übergeordneten Seite
     * @param array   $insertValues
     * @param string  $tableName
     * @param bool    $enableBlocks
     *
     * @return bool
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
     * @param integer $id
     *    ID des zu bearbeitenden Knotens
     * @param integer $parentId
     *    ID des neuen Elternelements
     * @param integer $blockId
     *    ID des neuen Blocks
     * @param array   $updateValues
     * @param string  $tableName
     * @param bool    $enableBlocks
     *
     * @return bool
     */
    public function editNode(
        $id,
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
                $id,
                $parentId,
                $blockId,
                $updateValues
            );
    }

    /**
     * Methode zum Umsortieren von Knoten
     *
     * @param integer $id
     * @param string  $mode
     *
     * @param string  $tableName
     * @param bool    $enableBlocks
     *
     * @return bool
     */
    public function sort($id, $mode, $tableName, $enableBlocks = false)
    {
        return $this->sort
            ->setTableName($tableName)
            ->setEnableBlocks($enableBlocks)
            ->execute($id, $mode);
    }
}
