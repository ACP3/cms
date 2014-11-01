<?php
namespace ACP3\Core\View\Renderer\Smarty\Plugins;

use ACP3\Core\Date;

/**
 * Class DateRange
 * @package ACP3\Core\View\Renderer\Smarty
 */
class DateRange extends AbstractPlugin
{
    /**
     * @var string
     */
    protected $pluginName = 'date_range';

    /**
     * @var \ACP3\Core\Date
     */
    protected $date;

    /**
     * @param Date $date
     */
    public function __construct(Date $date)
    {
        $this->date = $date;
    }

    /**
     * @param $params
     *
     * @return string
     */
    public function process($params)
    {
        $format = isset($params['format']) ? $params['format'] : 'long';

        if (isset($params['start']) && isset($params['end'])) {
            return $this->date->formatTimeRange($params['start'], $params['end'], $format);
        } else if (isset($params['start'])) {
            return $this->date->formatTimeRange($params['start'], '', $format);
        }

        return '';
    }
}