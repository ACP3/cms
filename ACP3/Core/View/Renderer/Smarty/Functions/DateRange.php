<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

/**
 * Class DateRange
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class DateRange extends AbstractFunction
{
    /**
     * @var \ACP3\Core\Helpers\Formatter\DateRange
     */
    protected $dateRangeFormatter;

    /**
     * @param \ACP3\Core\Helpers\Formatter\DateRange $dateRangeFormatter
     */
    public function __construct(\ACP3\Core\Helpers\Formatter\DateRange $dateRangeFormatter)
    {
        $this->dateRangeFormatter = $dateRangeFormatter;
    }

    /**
     * @inheritdoc
     */
    public function getPluginName()
    {
        return 'date_range';
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        $format = isset($params['format']) ? $params['format'] : 'long';

        if (isset($params['start']) && isset($params['end'])) {
            return $this->dateRangeFormatter->formatTimeRange($params['start'], $params['end'], $format);
        } elseif (isset($params['start'])) {
            return $this->dateRangeFormatter->formatTimeRange($params['start'], '', $format);
        }

        return '';
    }
}
