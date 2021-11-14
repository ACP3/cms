<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

abstract class AbstractColumnRenderer implements ColumnRendererInterface
{
    public const CELL_TYPE = 'td';

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
     * @var int|null
     */
    private $totalResults;

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
    public function getTotalResults(): ?int
    {
        return $this->totalResults;
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalResults(int $totalResults)
    {
        $this->totalResults = $totalResults;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow)
    {
        return $this->render($column, $this->getValue($column, $dbResultRow));
    }

    protected function render(array $column, ?string $value = ''): array|string
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

    private function renderAjax(array $column, string $value = ''): array|string
    {
        if (\is_array($column['attribute']) && \count($column['attribute'])) {
            $column['attribute']['_'] = $value;

            return $column['attribute'];
        }

        return $value;
    }

    protected function addHtmlAttribute(array|string $attributeName, ?string $attributeData = null): string
    {
        if (\is_array($attributeName)) {
            $data = '';
            foreach ($attributeName as $key => $value) {
                $data .= $this->addHtmlAttribute($key, $value);
            }

            return $data;
        }

        if ($attributeData !== null && $attributeData !== '') {
            if (\in_array($attributeName, static::mandatoryAttributes(), true)) {
                $attributeName = 'data-' . $attributeName;
            }

            return ' ' . $attributeName . '="' . $attributeData . '"';
        }

        return '';
    }

    protected function getFirstDbField(array $column): string
    {
        $fields = $this->getDbFields($column);

        return reset($fields);
    }

    /**
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
     * @param string $field
     *
     * @return string|null
     */
    protected function getDbValueIfExists(array $dbResultRow, $field)
    {
        return $dbResultRow[$field] ?? null;
    }

    protected function getDefaultValue(array $column): string
    {
        return $column['custom']['default_value'] ?? '';
    }

    protected function getDbFields(array $column): array
    {
        return $column['fields'];
    }

    public static function mandatoryAttributes(): array
    {
        return [];
    }
}
