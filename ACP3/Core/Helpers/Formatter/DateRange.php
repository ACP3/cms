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
        if (empty($end) || $start >= $end) {
            if (empty($end)) {
                $title = $this->date->format($start, $format);
            } else {
                $title = sprintf($this->lang->t('system', 'date_published_since'), $this->date->format($start, $format));
            }
            return $this->generateTimeTag($start, $format, $title);
        } else {
            $dateRange = $this->generateTimeTag($start, $format);
            $dateRange .= '&ndash;';
            $dateRange .= $this->generateTimeTag($end, $format);

            return $dateRange;
        }
    }

    /**
     * @param string $date
     * @param string $format
     * @param string $title
     *
     * @return string
     */
    protected function generateTimeTag($date, $format, $title = '')
    {
        $rfcDate = $this->date->format($date, 'c');
        $title = !empty($title) ? ' title="' . $title . '"' : '';
        return '<time datetime="' . $rfcDate . '"' . $title . '>' . $this->date->format($date, $format) . '</time>';
    }

}