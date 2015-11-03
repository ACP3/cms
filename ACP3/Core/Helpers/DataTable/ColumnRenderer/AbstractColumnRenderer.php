<?php
namespace ACP3\Core\Helpers\DataTable\ColumnRenderer;

/**
 * Class AbstractColumnRenderer
 * @package ACP3\Core\Helpers\DataTable\ColumnRenderer
 */
abstract class AbstractColumnRenderer implements ColumnRendererInterface
{
    /**
     * @inheritdoc
     */
    public function renderColumn(array $column, $dbResultRow = '', $type = self::TYPE_TD)
    {
        $attribute = $this->addHtmlAttribute($column['attribute']);
        $class = $this->addHtmlAttribute('class', $column['class']);
        $style = $this->addHtmlAttribute('style', $column['class']);

        return "<{$type}{$attribute}{$class}{$style}>{$dbResultRow}</{$type}>";
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
     *
     * @return array
     */
    protected function getDbFields(array $column)
    {
        return $column['fields'];
    }
}