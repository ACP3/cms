<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

class ReplaceValueColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var array
     */
    private $search = [];
    /**
     * @var array
     */
    private $replace = [];

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
    protected function getDbValueIfExists(array $dbResultRow, $field): ?string
    {
        return isset($dbResultRow[$field]) ? str_replace($this->search, $this->replace, $dbResultRow[$field]) : null;
    }
}
