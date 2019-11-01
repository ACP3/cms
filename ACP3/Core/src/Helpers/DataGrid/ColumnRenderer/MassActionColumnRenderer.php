<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

/**
 * @deprecated Since version 4.30.0, to be removed in 5.0.0. Use class ACP3\Core\DataGrid\ColumnRenderer\MassActionColumnRenderer instead
 */
class MassActionColumnRenderer extends AbstractColumnRenderer
{
    /**
     * {@inheritdoc}
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
