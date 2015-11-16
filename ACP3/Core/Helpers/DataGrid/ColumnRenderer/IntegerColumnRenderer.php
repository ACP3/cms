<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

/**
 * Class IntegerColumnRenderer
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
class IntegerColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @param array $column
     * @param array $dbResultRow
     *
     * @return string
     */
    protected function getDbFieldValueIfExists(array $column, array $dbResultRow)
    {
        $field = $this->getFirstDbField($column);

        if (isset($dbResultRow[$field])) {
            return (int)$dbResultRow[$field];
        }

        if (isset($column['custom']['default_value'])) {
            return $column['custom']['default_value'];
        }

        return '';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'integer';
    }
}