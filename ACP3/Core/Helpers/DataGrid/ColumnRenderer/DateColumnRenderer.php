<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

use ACP3\Core\Date;
use ACP3\Core\Helpers\Formatter\DateRange;

/**
 * @deprecated Since version 4.30.0, to be removed in 5.0.0. Use class ACP3\Core\DataGrid\ColumnRenderer\DateColumnRenderer instead
 */
class DateColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\Formatter\DateRange
     */
    protected $dateRangeHelper;

    /**
     * @param \ACP3\Core\Date                        $date
     * @param \ACP3\Core\Helpers\Formatter\DateRange $dateRangeHelper
     */
    public function __construct(
        Date $date,
        DateRange $dateRangeHelper
    ) {
        $this->date = $date;
        $this->dateRangeHelper = $dateRangeHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow)
    {
        $value = $this->getValue($column, $dbResultRow);

        if ($value !== null && $value !== $this->getDefaultValue($column)) {
            $field = $this->getFirstDbField($column);
            $column['attribute'] += [
                'data-order' => $this->date->format($dbResultRow[$field], 'U'),
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
    protected function getValue(array $column, array $dbResultRow)
    {
        $field = $this->getFirstDbField($column);
        $value = $this->getDbValueIfExists($dbResultRow, $field);

        if ($value === null) {
            $value = $this->getDefaultValue($column);
        } else {
            $fields = $this->getDbFields($column);
            $value = $this->dateRangeHelper->formatTimeRange($value, $this->getDbValueIfExists($dbResultRow, \next($fields)));
        }

        return $value;
    }
}
