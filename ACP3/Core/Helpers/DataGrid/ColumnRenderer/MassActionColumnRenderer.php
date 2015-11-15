<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

/**
 * Class MassActionColumnRenderer
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
class MassActionColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @inheritdoc
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow, $identifier, $primaryKey)
    {
        if ($column['custom']['can_delete'] === true) {
            $value = '<input type="checkbox" name="entries[]" value="' . $dbResultRow[$primaryKey] . '">';
            return $this->render($column, $value);
        }

        return '';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'mass_action';
    }
}