<?php
namespace ACP3\Core\Helpers\DataTable\ColumnRenderer;
use ACP3\Core\Date;
use ACP3\Core\Helpers\Formatter\DateRange;

/**
 * Class DateColumnRenderer
 * @package ACP3\Core\Helpers\DataTable\ColumnRenderer
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
        $date = $dbResultRow[$this->getFirstDbField($column)];
        $column['attribute'] = [
            'data-order' => $this->date->format($date, 'U')
        ];
        return parent::renderColumn(
            $column,
            $this->dateRangeHelper->formatTimeRange($date),
            $type
        );
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'date';
    }
}