<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

/**
 * Class ReplaceValueColumnRenderer
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
class ReplaceValueColumnRenderer extends AbstractColumnRenderer
{

    /**
     * @inheritdoc
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow)
    {
        $value = $this->getDbFieldValueIfExists($column, $dbResultRow);

        $search = $column['custom']['search'];
        $replace = $column['custom']['replace'];

        return $this->render($column, str_replace($search, $replace, $value));
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'replace_value';
    }
}