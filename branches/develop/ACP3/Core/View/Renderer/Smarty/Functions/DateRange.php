<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\Date;

/**
 * Class DateRange
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class DateRange extends AbstractFunction
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;

    /**
     * @param \ACP3\Core\Date $date
     */
    public function __construct(Date $date)
    {
        $this->date = $date;
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
            return $this->date->formatTimeRange($params['start'], $params['end'], $format);
        } elseif (isset($params['start'])) {
            return $this->date->formatTimeRange($params['start'], '', $format);
        }

        return '';
    }
}
