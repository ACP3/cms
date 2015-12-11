<?php

namespace ACP3\Core;

use ACP3\Core\I18n\Translator;

/**
 * Class Date
 * @package ACP3\Core
 */
class Date
{
    const DEFAULT_DATE_FORMAT_LONG = 'Y-m-d H:i';
    const DEFAULT_DATE_FORMAT_FULL = 'Y-m-d H:i:s';
    const DEFAULT_DATE_FORMAT_SHORT = 'Y-m-d';

    /**
     * @var string
     */
    protected $dateFormatLong = '';
    /**
     * @var string
     */
    protected $dateFormatShort = '';
    /**
     * @var \DateTimeZone
     */
    protected $dateTimeZone;

    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;

    /**
     * @param \ACP3\Core\User            $user
     * @param \ACP3\Core\I18n\Translator $translator
     * @param \ACP3\Core\Config          $config
     */
    public function __construct(
        User $user,
        Translator $translator,
        Config $config
    )
    {
        $this->translator = $translator;
        $this->config = $config;

        $this->_setFormatAndTimeZone($user->getUserInfo());
    }

    /**
     * @param array $userInfo
     */
    protected function _setFormatAndTimeZone(array $userInfo = [])
    {
        if (!empty($userInfo)) {
            $this->dateFormatLong = $userInfo['date_format_long'];
            $this->dateFormatShort = $userInfo['date_format_short'];
            $timeZone = $userInfo['time_zone'];
        } else {
            $settings = $this->config->getSettings('system');

            $this->dateFormatLong = $settings['date_format_long'];
            $this->dateFormatShort = $settings['date_format_short'];
            $timeZone = $settings['date_time_zone'];
        }
        $this->dateTimeZone = new \DateTimeZone($timeZone);
    }

    /**
     * @return string
     */
    public function getDateFormatLong()
    {
        return $this->dateFormatLong;
    }

    /**
     * @param string $dateFormatLong
     *
     * @return $this
     */
    public function setDateFormatLong($dateFormatLong)
    {
        $this->dateFormatLong = $dateFormatLong;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateFormatShort()
    {
        return $this->dateFormatShort;
    }

    /**
     * @param string $dateFormatShort
     *
     * @return $this
     */
    public function setDateFormatShort($dateFormatShort)
    {
        $this->dateFormatShort = $dateFormatShort;

        return $this;
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
        switch ($format) {
            case '':
            case 'short':
                $format = $this->dateFormatShort;
                break;
            case 'long':
                $format = $this->dateFormatLong;
                break;
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
        return strtr($dateTime->format($format), $this->localizeDaysAndMonths($format));
    }

    /**
     * @param string $format
     *
     * @return array
     */
    protected function localizeDaysAndMonths($format)
    {
        $replace = [];
        // Localize days
        if (strpos($format, 'D') !== false) {
            $replace = $this->localizeDaysAbbr();
        } elseif (strpos($format, 'l') !== false) {
            $replace = $this->localizeDays();
        }

        // Localize months
        if (strpos($format, 'M') !== false) {
            $replace = array_merge($replace, $this->localizeMonthsAbbr());
        } elseif (strpos($format, 'F') !== false) {
            $replace = array_merge($replace, $this->localizeMonths());
        }

        return $replace;
    }

    /**
     * @return array
     */
    protected function localizeDaysAbbr()
    {
        return [
            'Mon' => $this->translator->t('system', 'date_mon'),
            'Tue' => $this->translator->t('system', 'date_tue'),
            'Wed' => $this->translator->t('system', 'date_wed'),
            'Thu' => $this->translator->t('system', 'date_thu'),
            'Fri' => $this->translator->t('system', 'date_fri'),
            'Sat' => $this->translator->t('system', 'date_sat'),
            'Sun' => $this->translator->t('system', 'date_sun')
        ];
    }

    /**
     * @return array
     */
    protected function localizeDays()
    {
        return [
            'Monday' => $this->translator->t('system', 'date_monday'),
            'Tuesday' => $this->translator->t('system', 'date_tuesday'),
            'Wednesday' => $this->translator->t('system', 'date_wednesday'),
            'Thursday' => $this->translator->t('system', 'date_thursday'),
            'Friday' => $this->translator->t('system', 'date_friday'),
            'Saturday' => $this->translator->t('system', 'date_saturday'),
            'Sunday' => $this->translator->t('system', 'date_sunday')
        ];
    }

    /**
     * @return array
     */
    protected function localizeMonthsAbbr()
    {
        return [
            'Jan' => $this->translator->t('system', 'date_jan'),
            'Feb' => $this->translator->t('system', 'date_feb'),
            'Mar' => $this->translator->t('system', 'date_mar'),
            'Apr' => $this->translator->t('system', 'date_apr'),
            'May' => $this->translator->t('system', 'date_may_abbr'),
            'Jun' => $this->translator->t('system', 'date_jun'),
            'Jul' => $this->translator->t('system', 'date_jul'),
            'Aug' => $this->translator->t('system', 'date_aug'),
            'Sep' => $this->translator->t('system', 'date_sep'),
            'Oct' => $this->translator->t('system', 'date_oct'),
            'Nov' => $this->translator->t('system', 'date_nov'),
            'Dec' => $this->translator->t('system', 'date_dec')
        ];
    }

    /**
     * @return array
     */
    protected function localizeMonths()
    {
        return [
            'January' => $this->translator->t('system', 'date_january'),
            'February' => $this->translator->t('system', 'date_february'),
            'March' => $this->translator->t('system', 'date_march'),
            'April' => $this->translator->t('system', 'date_april'),
            'May' => $this->translator->t('system', 'date_may_full'),
            'June' => $this->translator->t('system', 'date_june'),
            'July' => $this->translator->t('system', 'date_july'),
            'August' => $this->translator->t('system', 'date_august'),
            'September' => $this->translator->t('system', 'date_september'),
            'October' => $this->translator->t('system', 'date_october'),
            'November' => $this->translator->t('system', 'date_november'),
            'December' => $this->translator->t('system', 'date_december')
        ];
    }

    /**
     * Gibt einen einfachen Zeitstempel zurück, welcher sich an UTC ausrichtet
     *
     * @param string $value
     * @param bool   $isLocalTime
     *
     * @return int
     */
    public function timestamp($value = 'now', $isLocalTime = false)
    {
        return (int)$this->format($value, 'U', true, $isLocalTime);
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
        return $this->format('now', self::DEFAULT_DATE_FORMAT_FULL, true, $isLocalTime);
    }

    /**
     * Gibt einen an UTC ausgerichteten Zeitstempel im MySQL DateTime Format zurück
     *
     * @param string $value
     *
     * @return string
     */
    public function toSQL($value = '')
    {
        return $this->format(empty($value) === true ? 'now' : $value, self::DEFAULT_DATE_FORMAT_FULL, true, false);
    }

    /**
     * Konvertiert einen Unixstamp in das MySQL-Datetime Format
     *
     * @param string $value
     * @param bool   $isLocalTime
     *
     * @return string
     */
    public function timestampToDateTime($value, $isLocalTime = false)
    {
        return $this->format($value, self::DEFAULT_DATE_FORMAT_FULL, true, $isLocalTime);
    }
}
