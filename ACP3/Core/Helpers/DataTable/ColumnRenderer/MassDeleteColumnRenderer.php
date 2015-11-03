<?php
namespace ACP3\Core\Helpers\DataTable\ColumnRenderer;

/**
 * Class MassDeleteColumnRenderer
 * @package ACP3\Core\Helpers\DataTable\ColumnRenderer
 */
class MassDeleteColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @inheritdoc
     */
    public function renderColumn(array $column, $dbResultRow = '', $type = self::TYPE_TD)
    {
        if ($column['custom']['can_delete'] === true) {
            $value = '<input type="checkbox" name="entries[]" value="' . $dbResultRow['id'] . '">';
            return parent::renderColumn($column, $value, $type);
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