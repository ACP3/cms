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
     * Gibt die Formularfelder f�r den Ver�ffentlichungszeitraum aus
     *
     * @param string $start
     * @param string $end
     * @param string $format
     *
     * @return string
     */
    public function formatTimeRange($start, $end = '', $format = 'long')
    {
        if ($end === '' || $start >= $end) {
            if ($end === '') {
                $title = $this->date->format($start, $format);
            } else {
                $title = sprintf($this->lang->t('system', 'date_published_since'), $this->date->format($start, $format));
            }
            return '<time datetime="' . $start . '" title="' . $title . '">' . $this->date->format($start, Date::DEFAULT_DATE_FORMAT_LONG) . '</time>';
        } else {
            $title = sprintf($this->lang->t('system', 'date_time_range'), $this->date->format($start, $format), $this->date->format($end, $format));
            return '<time datetime="' . $start . '/' . $end . '" title="' . $title . '">' . $this->date->format($start, Date::DEFAULT_DATE_FORMAT_LONG) . '&ndash;' . $this->date->format($end, $datetimeFormat) . '</time>';
        }
    }

}