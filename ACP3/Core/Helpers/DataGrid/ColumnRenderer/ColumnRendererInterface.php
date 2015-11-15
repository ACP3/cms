<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

/**
 * Interface ColumnRendererInterface
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
interface ColumnRendererInterface
{
    const TYPE_TH = 'th';
    const TYPE_TD = 'td';

    /**
     * @param array  $column
     * @param array  $dbResultRow
     * @param string $identifier
     * @param string $primaryKey
     *
     * @return string
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow, $identifier, $primaryKey);

    /**
     * @return string
     */
    public function getType();
}