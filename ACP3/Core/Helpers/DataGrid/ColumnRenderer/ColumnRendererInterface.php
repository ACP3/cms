<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

/**
 * @deprecated Since version 4.30.0, to be removed in 5.0.0. Use class ACP3\Core\DataGrid\ColumnRenderer\ColumnRendererInterface instead
 */
interface ColumnRendererInterface
{
    /**
     * @param array $column
     * @param array $dbResultRow
     *
     * @return string
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow);
}
