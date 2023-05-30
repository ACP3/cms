<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

class RoundNumberColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var int
     */
    private $precision = 0;

    public function fetchDataAndRenderColumn(array $column, array $dbResultRow): string|array
    {
        $this->precision = $column['custom']['precision'];

        return $this->render($column, $this->getValue($column, $dbResultRow));
    }

    protected function getDbValueIfExists(array $dbResultRow, string $field): ?string
    {
        return !empty($dbResultRow[$field]) ? (string) round($dbResultRow[$field], $this->precision) : null;
    }
}
