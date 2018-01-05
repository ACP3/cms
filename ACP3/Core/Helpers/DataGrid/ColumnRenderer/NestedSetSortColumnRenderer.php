<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

class NestedSetSortColumnRenderer extends SortColumnRenderer
{
    /**
     * @inheritdoc
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow)
    {
        $value = '';
        if ($dbResultRow['first'] === true && $dbResultRow['last'] === true) {
            $value = $this->fetchSortForbiddenHtml();
        } else {
            if ($dbResultRow['last'] === false) {
                $value .= $this->fetchSortDirectionHtml(
                    $this->router->route(\sprintf($column['custom']['route_sort_down'], $dbResultRow[$this->primaryKey])),
                    'down'
                );
            }
            if ($dbResultRow['first'] === false) {
                $value .= $this->fetchSortDirectionHtml(
                    $this->router->route(\sprintf($column['custom']['route_sort_up'], $dbResultRow[$this->primaryKey])),
                    'up'
                );
            }
        }

        $column['attribute'] += [
            'sort' => $dbResultRow[$this->getFirstDbField($column)],
        ];

        return $this->render($column, $value);
    }
}
