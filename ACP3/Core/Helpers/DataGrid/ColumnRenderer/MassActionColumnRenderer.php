<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

/**
 * Class MassActionColumnRenderer
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
class MassActionColumnRenderer extends AbstractColumnRenderer
{
    const NAME = 'mass_action';

    /**
     * @inheritdoc
     */
    protected function getValue(array $column, array $dbResultRow)
    {
        $value = null;
        if (isset($column['custom']['can_delete']) && $column['custom']['can_delete'] === true) {
            $value = '<input type="checkbox" name="entries[]" value="' . $dbResultRow[$this->primaryKey] . '">';
        }

        if ($value === null) {
            $value = $this->getDefaultValue($column);
        }

        return $value;
    }
}