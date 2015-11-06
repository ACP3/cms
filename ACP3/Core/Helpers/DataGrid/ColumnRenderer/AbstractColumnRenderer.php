<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

/**
 * Class AbstractColumnRenderer
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
abstract class AbstractColumnRenderer implements ColumnRendererInterface
{
    /**
     * @inheritdoc
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow)
    {
        return $this->render($column, $this->getDbFieldValueIfExists($column, $dbResultRow));
    }

    /**
     * @param array  $column
     * @param string $value
     * @param string $type
     *
     * @return string
     */
    protected function render(array $column, $value = '', $type = self::TYPE_TD)
    {
        $attribute = $this->addHtmlAttribute($column['attribute']);
        $class = $this->addHtmlAttribute('class', $column['class']);
        $style = $this->addHtmlAttribute('style', $column['class']);

        return "<{$type}{$attribute}{$class}{$style}>{$value}</{$type}>";
    }

    /**
     * @param string|array $attributeName
     * @param string|null  $attributeData
     *
     * @return string
     */
    protected function addHtmlAttribute($attributeName, $attributeData = null)
    {
        if (is_array($attributeName)) {
            $data = '';
            foreach ($attributeName as $key => $value) {
                $data .= $this->addHtmlAttribute($key, $value);
            }

            return $data;
        } elseif (!empty($attributeData)) {
            return ' ' . $attributeName . '="' . $attributeData . '"';
        }

        return '';
    }

    /**
     * @param array $column
     *
     * @return string
     */
    protected function getFirstDbField(array $column)
    {
        return reset($column['fields']);
    }

    /**
     * @param array $column
     * @param array $dbResultRow
     *
     * @return string
     */
    protected function getDbFieldValueIfExists(array $column, array $dbResultRow)
    {
        $field = $this->getFirstDbField($column);

        if (isset($dbResultRow[$field])) {
            return $dbResultRow[$field];
        }

        if (isset($column['custom']['default_value'])) {
            return $column['custom']['default_value'];
        }

        return '';
    }

    /**
     * @param array $column
     *
     * @return array
     */
    protected function getDbFields(array $column)
    {
        return $column['fields'];
    }
}