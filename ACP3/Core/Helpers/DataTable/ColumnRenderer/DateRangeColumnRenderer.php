<?php
namespace ACP3\Core\Helpers\DataTable\ColumnRenderer;

use ACP3\Core\Date;
use ACP3\Core\Helpers\Formatter\DateRange;

/**
 * Class DateRangeColumnRenderer
 * @package ACP3\Core\Helpers\DataTable\ColumnRenderer
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
    public function renderColumn(array $column, $dbResultRow = '', $type = self::TYPE_TD)
    {
        $dateStart = $dbResultRow[$this->getFirstDbField($column)];
        $dateEnd = $dbResultRow[$column['fields'][1]];
        $column['attribute'] = [
            'data-order' => $this->date->format($dateStart, 'U')
        ];

        return parent::renderColumn(
            $column,
            $this->dateRangeHelper->formatTimeRange($dateStart, $dateEnd),
            $type
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