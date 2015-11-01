<?php
namespace ACP3\Core\Helpers\Formatter;


use ACP3\Core\Date;
use ACP3\Core\Lang;

/**
 * Class DateRange
 * @package ACP3\Core\Helpers\Formatter
 */
class DateRange
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;

    /**
     * @param \ACP3\Core\Date $date
     * @param \ACP3\Core\Lang $lang
     */
    public function __construct(
        Date $date,
        Lang $lang
    )
    {
        $this->date = $date;
        $this->lang = $lang;
    }

    /**
     * Formats a given single date or date range into the desired format
     *
     * @param string $start
     * @param string $end
     * @param string $format
     *
     * @return string
     */
    public function formatTimeRange($start, $end = '', $format = 'long')
    {
        $rfcStart = $this->date->format($start, 'c');

        if ($end === '' || $start >= $end) {
            if ($end === '') {
                $title = $this->date->format($start, $format);
            } else {
                $title = sprintf($this->lang->t('system', 'date_published_since'), $this->date->format($start, $format));
            }
            return '<time datetime="' . $rfcStart . '" title="' . $title . '">' . $this->date->format($start, $format) . '</time>';
        } else {
            $rfcEnd = $this->date->format($end, 'c');

            $dateRange = '<time datetime="' . $rfcStart . '">';
            $dateRange.= $this->date->format($start, $format);
            $dateRange.= '</time>';
            $dateRange.= '&ndash;';
            $dateRange.= '<time datetime="' . $rfcEnd . '">';
            $dateRange.= $this->date->format($end, $format);
            $dateRange.= '</time>';

            return $dateRange;
        }
    }

}