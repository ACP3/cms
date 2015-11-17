<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

use ACP3\Core\Date;
use ACP3\Core\Helpers\Formatter\DateRange;

/**
 * Class DateRangeColumnRenderer
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
class DateRangeColumnRenderer extends AbstractColumnRenderer
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
     * DateRangeColumnRenderer constructor.
     *
     * @param \ACP3\Core\Date                        $date
     * @param \ACP3\Core\Helpers\Formatter\DateRange $dateRangeHelper
     */
    public function __construct(
        Date $date,
        DateRange $dateRangeHelper
    )
    {
        $this->date = $date;
        $this->dateRangeHelper = $dateRangeHelper;
    }

    /**
     * @inheritdoc
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow, $identifier, $primaryKey)
    {
        $dateStart = $this->getValue($column, $dbResultRow);
        $dateEnd = $dbResultRow[$column['fields'][1]];
        $column['attribute'] = [
            'data-order' => $this->date->format($dateStart, 'U')
        ];

        return $this->render(
            $column,
            $this->dateRangeHelper->formatTimeRange($dateStart, $dateEnd)
        );
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'date_range';
    }
}