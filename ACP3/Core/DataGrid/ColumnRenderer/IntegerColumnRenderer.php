<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

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
        return isset($dbResultRow[$field]) ? (int) $dbResultRow[$field] : null;
    }
}
