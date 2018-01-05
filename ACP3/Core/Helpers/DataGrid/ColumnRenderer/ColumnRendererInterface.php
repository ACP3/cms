<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

/**
 * Interface ColumnRendererInterface
 */
interface ColumnRendererInterface
{
    /**
     * @param string $identifier
     *
     * @return $this
     */
    public function setIdentifier(string $identifier);

    /**
     * @param string $primaryKey
     *
     * @return $this
     */
    public function setPrimaryKey(string $primaryKey);

    /**
     * @param bool $isAjax
     * @return $this
     */
    public function setIsAjax(bool $isAjax);

    /**
     * @param array $column
     * @param array $dbResultRow
     *
     * @return string
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow);
}
