<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

use ACP3\Core\Date;
use ACP3\Core\Helpers\Formatter\DateRange;

class DateColumnRenderer extends AbstractColumnRenderer
{
    public function __construct(protected Date $date, protected DateRange $dateRangeHelper)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow): string|array
    {
        $value = $this->getValue($column, $dbResultRow);

        if ($value !== null && $value !== $this->getDefaultValue($column)) {
            $field = $this->getFirstDbField($column);
            $column['attribute'] += [
                'sort' => $this->date->format($dbResultRow[$field], 'U'),
            ];
        }

        return $this->render(
            $column,
            $value
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getValue(array $column, array $dbResultRow): ?string
    {
        $field = $this->getFirstDbField($column);
        $value = $this->getDbValueIfExists($dbResultRow, $field);

        if ($value === null) {
            $value = $this->getDefaultValue($column);
        } else {
            $fields = $this->getDbFields($column);
            $value = $this->dateRangeHelper->formatTimeRange($value, $this->getDbValueIfExists($dbResultRow, next($fields)));
        }

        return $value;
    }

    public static function mandatoryAttributes(): array
    {
        return ['sort', '_'];
    }
}
