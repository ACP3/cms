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

    /**
     * {@inheritdoc}
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow)
    {
        $this->precision = $column['custom']['precision'];

        return $this->render($column, $this->getValue($column, $dbResultRow));
    }

    /**
     * {@inheritdoc}
     */
    protected function getDbValueIfExists(array $dbResultRow, $field): ?string
    {
        return !empty($dbResultRow[$field]) ? (string) round($dbResultRow[$field], $this->precision) : null;
    }
}
