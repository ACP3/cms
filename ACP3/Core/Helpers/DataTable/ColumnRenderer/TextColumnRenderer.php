<?php
namespace ACP3\Core\Helpers\DataTable\ColumnRenderer;

/**
 * Class TextColumnRenderer
 * @package ACP3\Core\Helpers\DataTable\ColumnRenderer
 */
class TextColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @inheritdoc
     */
    public function renderColumn(array $column, $dbResultRow = '', $type = self::TYPE_TD)
    {
        return parent::renderColumn(
            $column,
            $dbResultRow[$this->getFirstDbField($column)],
            $type
        );
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'text';
    }
}