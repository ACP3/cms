<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

/**
 * Class ReplaceValueColumnRenderer
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
class ReplaceValueColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var array
     */
    protected $search = [];
    /**
     * @var array
     */
    protected $replace = [];

    /**
     * @inheritdoc
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow)
    {
        $this->search = $column['custom']['search'];
        $this->replace = $column['custom']['replace'];

        return $this->render($column, $this->getValue($column, $dbResultRow));
    }

    /**
     * @inheritdoc
     */
    protected function getDbValueIfExists(array $dbResultRow, $field)
    {
        return isset($dbResultRow[$field]) ? str_replace($this->search, $this->replace, $dbResultRow[$field]) : null;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'replace_value';
    }
}