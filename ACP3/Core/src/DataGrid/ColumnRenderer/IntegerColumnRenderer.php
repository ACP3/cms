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
    protected function getDbValueIfExists(array $dbResultRow, string $field): ?string
    {
        return \array_key_exists($field, $dbResultRow) && $dbResultRow[$field] !== null ? (string) (int) $dbResultRow[$field] : null;
    }
}
