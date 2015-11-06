<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

/**
 * Class PictureColumnRenderer
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
class PictureColumnRenderer extends AbstractColumnRenderer
{

    /**
     * @inheritdoc
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow)
    {
        if (isset($column['custom']['pattern'])) {
            $dbValue = $this->getDbFieldValueIfExists($column, $dbResultRow);
            $value = '<img src="' . sprintf($column['custom']['pattern'], $dbValue) . '" alt="">';
        } else {
            $value = '';
        }

        return $this->render($column, $value);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'picture';
    }
}