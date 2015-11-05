<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

/**
 * Class MassDeleteColumnRenderer
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
class MassDeleteColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @inheritdoc
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow)
    {
        if ($column['custom']['can_delete'] === true) {
            $value = '<input type="checkbox" name="entries[]" value="' . $dbResultRow['id'] . '">';
            return $this->render($column, $value);
        }

        return '';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'mass_delete';
    }
}