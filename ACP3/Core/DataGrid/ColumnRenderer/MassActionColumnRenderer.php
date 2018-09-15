<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

class MassActionColumnRenderer extends AbstractColumnRenderer
{
    /**
     * {@inheritdoc}
     */
    protected function getValue(array $column, array $dbResultRow)
    {
        $value = null;
        if (isset($column['custom']['can_delete']) && $column['custom']['can_delete'] === true) {
            $value = '<input type="checkbox" name="entries[]" value="' . $dbResultRow[$this->getPrimaryKey()] . '">';
        }

        if ($value === null) {
            $value = $this->getDefaultValue($column);
        }

        return $value;
    }
}
