<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

/**
 * Interface ColumnRendererInterface
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
interface ColumnRendererInterface
{
    /**
     * @param array $column
     * @param array $dbResultRow
     *
     * @return string
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow);
}