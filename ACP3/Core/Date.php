<?php

namespace ACP3\Core;

/**
 * Stellt Funktionen zur Datumsformatierung und Ausrichtung an den Zeitzonen bereit
 *
 * @author Tino Goratsch
 */
class Date
{

    /**
     * Langes Datumsformat
     *
     * @var string
     */
    protected $dateFormatLong = CONFIG_DATE_FORMAT_LONG;

    /**
     * Kurzes Datumsformat
     *
     * @var string
     */
    protected $dateFormatShort = CONFIG_DATE_FORMAT_SHORT;

    /**
     * PHP DateTimeZone-Object
     *
     * @var object
     */
    protected $dateTimeZone = null;

    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\Validator\Rules\Date
     */
    protected $dateValidator;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;

    /**
     * Falls man sich als User authentifiziert hat, eingestellte Zeitzone + Sommerzeiteinstellung holen
     *
     * @param Auth                 $auth
     * @param Lang                 $lang
     * @param Validator\Rules\Date $dateValidator
     * @param View                 $view
     */
    function __construct(
        Auth $auth,
        Lang $lang,
        \ACP3\Core\Validator\Rules\Date $dateValidator,
        View $view
    )
    {
        $info = $auth->getUserInfo();

        $this->lang = $lang;
        $this->dateValidator = $dateValidator;
        $this->view = $view;

        if (!empty($info)) {
            $this->dateFormatLong = $info['date_format_long'];
            $this->dateFormatShort = $info['date_format_short'];
            $timeZone = $info['time_zone'];
        } else {
            $timeZone = CONFIG_DATE_TIME_ZONE;
        }
        $this->dateTimeZone = new \DateTimeZone($timeZone);
    }

    /**
     * Liefert ein Array mit allen Zeitzonen dieser Welt aus
     *
     * @param string $currentValue
     *
     * @return array
     */
    public static function getTimeZones($currentValue = '')
    {
        $timeZones = array(
            'Africa' => \DateTimeZone::listIdentifiers(\DateTimeZone::AFRICA),
            'America' => \DateTimeZone::listIdentifiers(\DateTimeZone::AMERICA),
            'Antarctica' => \DateTimeZone::listIdentifiers(\DateTimeZone::ANTARCTICA),
            'Arctic' => \DateTimeZone::listIdentifiers(\DateTimeZone::ARCTIC),
            'Asia' => \DateTimeZone::listIdentifiers(\DateTimeZone::ASIA),
            'Atlantic' => \DateTimeZone::listIdentifiers(\DateTimeZone::ATLANTIC),
            'Australia' => \DateTimeZone::listIdentifiers(\DateTimeZone::AUSTRALIA),
            'Europe' => \DateTimeZone::listIdentifiers(\DateTimeZone::EUROPE),
            'Indian' => \DateTimeZone::listIdentifiers(\DateTimeZone::INDIAN),
            'Pacitic' => \DateTimeZone::listIdentifiers(\DateTimeZone::PACIFIC),
            'UTC' => \DateTimeZone::listIdentifiers(\DateTimeZone::UTC),
        );

        foreach ($timeZones as $key => $values) {
            $i = 0;
            foreach ($values as $row) {
                unset($timeZones[$key][$i]);
                $timeZones[$key][$row]['selected'] = Functions::selectEntry('date_time_zone', $row, $currentValue);
                ++$i;
            }
        }
        return $timeZones;
    }

    /**
     * Gibts ein Array mit den möglichen Datumsformaten aus,
     * um diese als Dropdownmenü darstellen zu können
     *
     * @param string $format
     *    Optionaler Parameter für das aktuelle Datumsformat
     *
     * @return array
     */
    public function dateFormatDropdown($format = '')
    {
        $dateformatLang = array(
            $this->lang->t('system', 'date_format_short'),
            $this->lang->t('system', 'date_format_long')
        );
        return Functions::selectGenerator('dateformat', array('short', 'long'), $dateformatLang, $format);
    }

    /**
     * Zeigt Dropdown-Menüs für die Veröffentlichungsdauer von Inhalten an
     *
     * @param mixed    $name
     *    Name des jeweiligen Inputfeldes
     * @param mixed    $value
     *    Der Zeitstempel des jeweiligen Eintrages
     * @param string   $format
     *    Das anzuzeigende Format im Textfeld
     * @param array    $params
     *    Dient dem Festlegen von weiteren Parametern
     * @param integer  $range
     *    1 = Start- und Enddatum anzeigen
     *    2 = Einfaches Inputfeld mitsamt Datepicker anzeigen
     * @param bool|int $withTime
     * @param bool     $inputFieldOnly
     *
     * @return string
     */
    public function datepicker(
        $name,
        $value = '',
        $format = 'Y-m-d H:i',
        array $params = array(),
        $range = 1,
        $withTime = true,
        $inputFieldOnly = false
    )
    {
        $datepicker = array(
            'range' => is_array($name) === true && $range === 1 ? 1 : 0,
            'with_time' => (bool)$withTime,
            'length' => $withTime === true ? 16 : 10,
            'input_only' => (bool)$inputFieldOnly,
            'params' => array(
                'firstDay' => '\'1\'',
                'dateFormat' => '\'yy-mm-dd\'',
                'constrainInput' => 'true',
                'changeMonth' => 'true',
                'changeYear' => 'true',
            )
        );
        if ($withTime === true) {
            $datepicker['params']['timeFormat'] = '\'HH:mm\'';
        }

        // Zusätzliche Datepicker-Parameter hinzufügen
        if (!empty($params) && is_array($params) === true) {
            $datepicker['params'] = array_merge($datepicker['params'], $params);
        }

        // Veröffentlichungszeitraum
        if (is_array($name) === true && $range === 1) {
            if (!empty($_POST[$name[0]]) && !empty($_POST[$name[1]])) {
                $valueStart = $_POST[$name[0]];
                $valueEnd = $_POST[$name[1]];
                $valueStartR = $this->format($_POST[$name[0]], 'r', false);
                $valueEndR = $this->format($_POST[$name[1]], 'r', false);
            } elseif (is_array($value) === true && $this->dateValidator->date($value[0], $value[1]) === true) {
                $valueStart = $this->format($value[0], $format);
                $valueEnd = $this->format($value[1], $format);
                $valueStartR = $this->format($value[0], 'r');
                $valueEndR = $this->format($value[1], 'r');
            } else {
                $valueStart = $this->format('now', $format, false);
                $valueEnd = $this->format('now', $format, false);
                $valueStartR = $this->format('now', 'r', false);
                $valueEndR = $this->format('now', 'r', false);
            }

            $datepicker['name_start'] = $name[0];
            $datepicker['name_end'] = $name[1];
            $datepicker['value_start'] = $valueStart;
            $datepicker['value_start_r'] = $valueStartR;
            $datepicker['value_end'] = $valueEnd;
            $datepicker['value_end_r'] = $valueEndR;
            // Einfaches Inputfeld mit Datepicker
        } else {
            if (!empty($_POST[$name])) {
                $value = $_POST[$name];
            } elseif ($this->dateValidator->date($value) === true) {
                $value = $this->format($value, $format);
            } else {
                $value = $this->format('now', $format, false);
            }

            $datepicker['name'] = $name;
            $datepicker['value'] = $value;
        }

        $this->view->assign('datepicker', $datepicker);

        return $this->view->fetchTemplate('system/date.tpl');
    }

    /**
     * Gibt ein formatiertes Datum zurück
     *
     * @param string $time
     * @param string $format
     * @param bool   $toLocalTimeZone
     * @param bool   $isLocalTimeZone
     *
     * @return string
     */
    public function format($time = 'now', $format = 'long', $toLocalTimeZone = true, $isLocalTimeZone = true)
    {
        // Datum in gewünschter Formatierung ausgeben
        switch ($format) {
            case '':
            case 'long':
                $format = $this->dateFormatLong;
                break;
            case 'short':
                $format = $this->dateFormatShort;
                break;
        }

        $replace = array();
        // Wochentage lokalisieren
        if (strpos($format, 'D') !== false) {
            $replace = $this->localizeDaysAbbr();
        } elseif (strpos($format, 'l') !== false) {
            $replace = $this->localizeDays();
        }

        // Monate lokalisieren
        if (strpos($format, 'M') !== false) {
            $replace = array_merge($replace, $this->localizeMonthsAbbr());
        } elseif (strpos($format, 'F') !== false) {
            $replace = array_merge($replace, $this->localizeMonths());
        }

        if (is_numeric($time)) {
            $time = date('c', $time);
        }

        $dateTime = new \DateTime($time, $this->dateTimeZone);
        if ($toLocalTimeZone === true) {
            if ($isLocalTimeZone === true) {
                $dateTime->setTimestamp($dateTime->getTimestamp() + $dateTime->getOffset());
            } else {
                $dateTime->setTimestamp($dateTime->getTimestamp() - $dateTime->getOffset());
            }
        }
        return strtr($dateTime->format($format), $replace);
    }

    /**
     * @return array
     */
    protected function localizeDaysAbbr()
    {
        return array(
            'Mon' => $this->lang->t('system', 'date_mon'),
            'Tue' => $this->lang->t('system', 'date_tue'),
            'Wed' => $this->lang->t('system', 'date_wed'),
            'Thu' => $this->lang->t('system', 'date_thu'),
            'Fri' => $this->lang->t('system', 'date_fri'),
            'Sat' => $this->lang->t('system', 'date_sat'),
            'Sun' => $this->lang->t('system', 'date_sun')
        );
    }

    /**
     * @return array
     */
    protected function localizeDays()
    {
        return array(
            'Monday' => $this->lang->t('system', 'date_monday'),
            'Tuesday' => $this->lang->t('system', 'date_tuesday'),
            'Wednesday' => $this->lang->t('system', 'date_wednesday'),
            'Thursday' => $this->lang->t('system', 'date_thursday'),
            'Friday' => $this->lang->t('system', 'date_friday'),
            'Saturday' => $this->lang->t('system', 'date_saturday'),
            'Sunday' => $this->lang->t('system', 'date_sunday')
        );
    }

    /**
     * @return array
     */
    protected function localizeMonthsAbbr()
    {
        return array(
            'Jan' => $this->lang->t('system', 'date_jan'),
            'Feb' => $this->lang->t('system', 'date_feb'),
            'Mar' => $this->lang->t('system', 'date_mar'),
            'Apr' => $this->lang->t('system', 'date_apr'),
            'May' => $this->lang->t('system', 'date_may_abbr'),
            'Jun' => $this->lang->t('system', 'date_jun'),
            'Jul' => $this->lang->t('system', 'date_jul'),
            'Aug' => $this->lang->t('system', 'date_aug'),
            'Sep' => $this->lang->t('system', 'date_sep'),
            'Oct' => $this->lang->t('system', 'date_oct'),
            'Nov' => $this->lang->t('system', 'date_nov'),
            'Dec' => $this->lang->t('system', 'date_dec')
        );
    }

    /**
     * @return array
     */
    protected function localizeMonths()
    {
        return array(
            'January' => $this->lang->t('system', 'date_january'),
            'February' => $this->lang->t('system', 'date_february'),
            'March' => $this->lang->t('system', 'date_march'),
            'April' => $this->lang->t('system', 'date_april'),
            'May' => $this->lang->t('system', 'date_may_full'),
            'June' => $this->lang->t('system', 'date_june'),
            'July' => $this->lang->t('system', 'date_july'),
            'August' => $this->lang->t('system', 'date_august'),
            'September' => $this->lang->t('system', 'date_september'),
            'October' => $this->lang->t('system', 'date_october'),
            'November' => $this->lang->t('system', 'date_november'),
            'December' => $this->lang->t('system', 'date_december')
        );
    }

    /**
     * Gibt die Formularfelder für den Veröffentlichungszeitraum aus
     *
     * @param string $start
     * @param string $end
     * @param string $format
     *
     * @return string
     */
    public function formatTimeRange($start, $end = '', $format = 'long')
    {
        $datetimeFormat = 'Y-m-d H:i';
        if ($end === '' || $start >= $end) {
            $title = $end === '' ? $this->format($start, $format) : sprintf($this->lang->t('system', 'date_published_since'), $this->format($start, $format));
            return '<time datetime="' . $start . '" title="' . $title . '">' . $this->format($start, $datetimeFormat) . '</time>';
        } else {
            $title = sprintf($this->lang->t('system', 'date_time_range'), $this->format($start, $format), $this->format($end, $format));
            return '<time datetime="' . $start . '/' . $end . '" title="' . $title . '">' . $this->format($start, $datetimeFormat) . '&ndash;' . $this->format($end, $datetimeFormat) . '</time>';
        }
    }

    /**
     * Gibt einen einfachen Zeitstempel zurück, welcher sich an UTC ausrichtet
     *
     * @param string $value
     * @param bool   $islocalTime
     *
     * @return integer
     */
    public function timestamp($value = 'now', $islocalTime = false)
    {
        return $this->format($value, 'U', true, $islocalTime);
    }

    /**
     * Gibt die aktuelle Uhrzeit im MySQL-Datetime Format zurück
     *
     * @param bool $isLocalTime
     *
     * @return string
     */
    public function getCurrentDateTime($isLocalTime = false)
    {
        return $this->format('now', 'Y-m-d H:i:s', true, $isLocalTime);
    }

    /**
     * Gibt einen an UTC ausgerichteten Zeitstempelim MySQL DateTime Format zurück
     *
     * @param string $value
     *
     * @return string
     */
    public function toSQL($value = '')
    {
        return $this->format(empty($value) === true ? 'now' : $value, 'Y-m-d H:i:s', true, false);
    }

    /**
     * Konvertiert einen Unixstamp in das MySQL-Datetime Format
     *
     * @param      $value
     * @param bool $isLocalTime
     *
     * @return string
     */
    public function timestampToDateTime($value, $isLocalTime = false)
    {
        return $this->format($value, 'Y-m-d H:i:s', true, $isLocalTime);
    }

}
