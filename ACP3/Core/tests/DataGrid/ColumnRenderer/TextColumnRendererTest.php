<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

class TextColumnRendererTest extends AbstractColumnRendererTest
{
    protected function setup(): void
    {
        $this->columnRenderer = new TextColumnRenderer();

        parent::setUp();
    }
}
