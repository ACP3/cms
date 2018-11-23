<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

abstract class AbstractColumnRenderer implements ColumnRendererInterface
{
    const CELL_TYPE = 'td';

    /**
     * @var string
     */
    private $identifier = '';
    /**
     * @var string|null
     */
    private $primaryKey;
    /**
     * @var bool
     */
    private $useAjax = false;

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrimaryKey(?string $primaryKey): self
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUseAjax(): bool
    {
        return $this->useAjax;
    }

    /**
     * {@inheritdoc}
     */
    public function setUseAjax(bool $useAjax): self
    {
        $this->useAjax = $useAjax;

        return $this;
    }

    /**
     * {@inheritdoc}
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
        if ($this->getUseAjax()) {
            return $this->renderAjax($column, $value);
        }

        $type = static::CELL_TYPE;
        $attribute = $this->addHtmlAttribute($column['attribute']);
        $class = $this->addHtmlAttribute('class', $column['class']);
        $style = $this->addHtmlAttribute('style', $column['style']);

        return "<{$type}{$attribute}{$class}{$style}>{$value}</{$type}>";
    }

    /**
     * @param array  $column
     * @param string $value
     *
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
    protected function addHtmlAttribute($attributeName, $attributeData = null)
    {
        if (\is_array($attributeName)) {
            $data = '';
            foreach ($attributeName as $key => $value) {
                $data .= $this->addHtmlAttribute($key, $value);
            }

            return $data;
        } elseif ($attributeData !== null && $attributeData !== '') {
            if (\in_array($attributeName, static::mandatoryAttributes())) {
                $attributeName = 'data-' . $attributeName;
            }

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
     * @return mixed
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

    public static function mandatoryAttributes(): array
    {
        return [];
    }
}
