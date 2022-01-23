<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

class ReplaceValueColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var string[]
     */
    private array $search = [];
    /**
     * @var string[]
     */
    private array $replace = [];

    /**
     * {@inheritdoc}
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow): string|array
    {
        $this->search = $column['custom']['search'];
        $this->replace = $column['custom']['replace'];

        return $this->render($column, $this->getValue($column, $dbResultRow));
    }

    /**
     * {@inheritdoc}
     */
    protected function getDbValueIfExists(array $dbResultRow, string $field): ?string
    {
        return isset($dbResultRow[$field]) ? str_replace($this->search, $this->replace, $dbResultRow[$field]) : null;
    }
}
