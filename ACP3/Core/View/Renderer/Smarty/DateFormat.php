<?php
namespace ACP3\Core\View\Renderer\Smarty;

use ACP3\Core\Date;

/**
 * Class DateFormat
 * @package ACP3\Core\View\Renderer\Smarty
 */
class DateFormat extends AbstractPlugin
{
    /**
     * @var string
     */
    protected $pluginName = 'date_format';

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

        if (isset($params['date'])) {
            return $this->date->format($params['date'], $format);
        }

        return '';
    }
}