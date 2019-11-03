<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\Date\DateTranslator;

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
     * @var \ACP3\Core\Date\DateTranslator
     */
    protected $dateTranslator;

    public function __construct(
        DateTranslator $dateTranslator
    ) {
        $this->dateTranslator = $dateTranslator;
    }

    /**
     * @return string
     */
    public function getDateFormatLong()
    {
        return $this->dateFormatLong;
    }

    /**
     * @return $this
     */
    public function setDateFormatLong(string $dateFormatLong)
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
     * @return $this
     */
    public function setDateFormatShort(string $dateFormatShort)
    {
        $this->dateFormatShort = $dateFormatShort;

        return $this;
    }

    /**
     * @return $this
     */
    public function setDateTimeZone(\DateTimeZone $dateTimeZone)
    {
        $this->dateTimeZone = $dateTimeZone;

        return $this;
    }

    /**
     * Gibt ein formatiertes Datum zurück.
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

        if (\is_numeric($time)) {
            $time = \date('c', $time);
        }

        $dateTime = new \DateTime($time, $this->dateTimeZone);
        if ($toLocalTimeZone === true) {
            if ($isLocalTimeZone === true) {
                $dateTime->setTimestamp($dateTime->getTimestamp() + $dateTime->getOffset());
            } else {
                $dateTime->setTimestamp($dateTime->getTimestamp() - $dateTime->getOffset());
            }
        }

        return \strtr($dateTime->format($format), $this->dateTranslator->localize($format));
    }

    /**
     * Gibt einen einfachen Zeitstempel zurück, welcher sich an UTC ausrichtet.
     *
     * @param string $value
     * @param bool   $isLocalTime
     *
     * @return int
     */
    public function timestamp($value = 'now', $isLocalTime = false)
    {
        return (int) $this->format($value, 'U', true, $isLocalTime);
    }

    /**
     * Gibt die aktuelle Uhrzeit im MySQL-Datetime Format zurück.
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
     * Gibt einen an UTC ausgerichteten Zeitstempel im MySQL DateTime Format zurück.
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
     * Konvertiert einen Unixstamp in das MySQL-Datetime Format.
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

    /**
     * @param string|int $time
     *
     * @return \DateTime
     */
    public function toDateTime($time = 'now')
    {
        if (\is_numeric($time)) {
            $time = \date('c', $time);
        }

        return new \DateTime($time, $this->dateTimeZone);
    }
}
