<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

/**
 * Class IntegerColumnRenderer
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
class IntegerColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @param array  $dbResultRow
     * @param string $field
     *
     * @return int|null
     */
    protected function getDbValueIfExists(array $dbResultRow, $field)
    {
        return isset($dbResultRow[$field]) ? (int)$dbResultRow[$field] : null;
    }
}