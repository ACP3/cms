<?php
namespace ACP3\Core\Helpers\DataTable\ColumnRenderer;

/**
 * Class HeaderColumnRenderer
 * @package ACP3\Core\Helpers\DataTable\ColumnRenderer
 */
class HeaderColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @return string
     */
    public function getType()
    {
        return 'table_header';
    }
}