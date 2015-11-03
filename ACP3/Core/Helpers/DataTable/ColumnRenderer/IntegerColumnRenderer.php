<?php
namespace ACP3\Core\Helpers\DataTable\ColumnRenderer;

/**
 * Class IntegerColumnRenderer
 * @package ACP3\Core\Helpers\DataTable\ColumnRenderer
 */
class IntegerColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @inheritdoc
     */
    public function renderColumn(array $column, $dbResultRow = '', $type = self::TYPE_TD)
    {
        return parent::renderColumn(
            $column,
            (int)$dbResultRow[$this->getFirstDbField($column)],
            $type
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