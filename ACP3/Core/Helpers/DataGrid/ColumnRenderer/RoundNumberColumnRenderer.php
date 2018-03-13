<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;


class RoundNumberColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var int
     */
    protected $precision = 0;

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
    protected function getDbValueIfExists(array $dbResultRow, $field)
    {
        return isset($dbResultRow[$field]) ? \round($dbResultRow[$field], $this->precision) : null;
    }
}
