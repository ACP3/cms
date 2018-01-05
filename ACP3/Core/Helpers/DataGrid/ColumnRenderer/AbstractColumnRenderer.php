<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

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
     * @var bool
     */
    protected $isAjax = false;

    /**
     * @inheritdoc
     */
    public function setIdentifier(string $identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setPrimaryKey(string $primaryKey)
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setIsAjax(bool $isAjax)
    {
        $this->isAjax = $isAjax;

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
        if ($this->isAjax) {
            return $this->renderAjax($column, $value);
        }

        $type = static::CELL_TYPE;
        $attribute = $this->addHtmlAttribute($column['attribute']);
        $class = $this->addHtmlAttribute('class', $column['class']);

        return "<{$type}{$attribute}{$class}>{$value}</{$type}>";
    }

    /**
     * @param array $column
     * @param string $value
     * @return string|array
     */
    private function renderAjax(array $column, string $value = '')
    {
        if (\is_array($column['attribute']) && \count($column['attribute'])) {
            $column['attribute']['_'] = $value;

            return $column['attribute'];
        }

        return $value;
    }

    /**
     * @param string|array $attributeName
     * @param string|null  $attributeData
     *
     * @return string
     */
    private function addHtmlAttribute($attributeName, $attributeData = null)
    {
        if (\is_array($attributeName)) {
            $data = '';
            foreach ($attributeName as $key => $value) {
                $data .= $this->addHtmlAttribute('data-' . $key, $value);
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

        return \reset($fields);
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
        return $dbResultRow[$field] ?? null;
    }

    /**
     * @param array $column
     *
     * @return string
     */
    protected function getDefaultValue(array $column)
    {
        return $column['custom']['default_value'] ?? '';
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
