<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

/**
 * Class IntegerColumnRenderer
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
class IntegerColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @inheritdoc
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow, $identifier, $primaryKey)
    {
        return parent::render(
            $column,
            (int)$this->getDbFieldValueIfExists($column, $dbResultRow)
        );
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'integer';
    }
}