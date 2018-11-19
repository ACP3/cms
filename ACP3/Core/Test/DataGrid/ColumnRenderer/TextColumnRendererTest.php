<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Test\DataGrid\ColumnRenderer;

use ACP3\Core\DataGrid\ColumnRenderer\TextColumnRenderer;

class TextColumnRendererTest extends AbstractColumnRendererTest
{
    protected function setUp()
    {
        $this->columnRenderer = new TextColumnRenderer();

        parent::setUp();
    }
}
