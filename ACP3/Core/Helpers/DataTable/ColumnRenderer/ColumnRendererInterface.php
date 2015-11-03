<?php
namespace ACP3\Core\Helpers\DataTable\ColumnRenderer;

/**
 * Interface ColumnRendererInterface
 * @package ACP3\Core\Helpers\DataTable\ColumnRenderer
 */
interface ColumnRendererInterface
{
    const TYPE_TH = 'th';
    const TYPE_TD = 'td';

    /**
     * @param array        $column
     * @param array|string $dbResultRow
     * @param string       $type
     *
     * @return string
     */
    public function renderColumn(array $column, $dbResultRow = '', $type = self::TYPE_TD);

    /**
     * @return string
     */
    public function getType();
}