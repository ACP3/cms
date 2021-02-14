<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

class IntegerColumnRenderer extends AbstractColumnRenderer
{
    /**
     * {@inheritDoc}
     */
    protected function getDbValueIfExists(array $dbResultRow, $field): ?string
    {
        return !empty($dbResultRow[$field]) ? (string) (int) $dbResultRow[$field] : null;
    }
}
