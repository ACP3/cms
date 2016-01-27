<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

/**
 * Class AbstractColumnRenderer
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
abstract class AbstractColumnRenderer implements ColumnRendererInterface
{
    const CELL_TYPE = 'td';

    /**
     * @var string
     */
    protected $identifier = '';
    /**
     * @var string
     */
    protected $primaryKey = '';

    /**
     * @param string $identifier
     *
     * @return $this
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @param mixed $primaryKey
     *
     * @return $this
     */
    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow)
    {
        return $this->render($column, $this->getValue($column, $dbResultRow));
    }

    /**
     * @param array  $column
     * @param string $value
     *
     * @return string
     */
    protected function render(array $column, $value = '')
    {
        $type = static::CELL_TYPE;
        $attribute = $this->addHtmlAttribute($column['attribute']);
        $class = $this->addHtmlAttribute('class', $column['class']);
        $style = $this->addHtmlAttribute('style', $column['style']);

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
        $fields = $this->getDbFields($column);

        return reset($fields);
    }

    /**
     * @param array $column
     * @param array $dbResultRow
     *
     * @return string
     */
    protected function getValue(array $column, array $dbResultRow)
    {
        $field = $this->getFirstDbField($column);
        $value = $this->getDbValueIfExists($dbResultRow, $field);

        if ($value === null) {
            $value = $this->getDefaultValue($column);
        }

        return $value;
    }

    /**
     * @param array  $dbResultRow
     * @param string $field
     *
     * @return string|null
     */
    protected function getDbValueIfExists(array $dbResultRow, $field)
    {
        return isset($dbResultRow[$field]) ? $dbResultRow[$field] : null;
    }

    /**
     * @param array $column
     *
     * @return string
     */
    protected function getDefaultValue(array $column)
    {
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