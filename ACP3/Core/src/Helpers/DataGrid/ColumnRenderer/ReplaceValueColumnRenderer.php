<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

/**
 * @deprecated Since version 4.30.0, to be removed in 5.0.0. Use class ACP3\Core\DataGrid\ColumnRenderer\ReplaceValueColumnRenderer instead
 */
class ReplaceValueColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var array
     */
    protected $search = [];
    /**
     * @var array
     */
    protected $replace = [];

    /**
     * {@inheritdoc}
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow)
    {
        $this->search = $column['custom']['search'];
        $this->replace = $column['custom']['replace'];

        return $this->render($column, $this->getValue($column, $dbResultRow));
    }

    /**
     * {@inheritdoc}
     */
    protected function getDbValueIfExists(array $dbResultRow, $field)
    {
        return isset($dbResultRow[$field]) ? \str_replace($this->search, $this->replace, $dbResultRow[$field]) : null;
    }
}
