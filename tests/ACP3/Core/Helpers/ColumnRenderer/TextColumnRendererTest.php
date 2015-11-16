<?php

class TextColumnRendererTest extends AbstractColumnRendererTest
{
    protected function setUp()
    {
        $this->columnRenderer = new \ACP3\Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer();
    }
}