<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\Date\DateTranslator;

class Date
{
    public const DEFAULT_DATE_FORMAT_LONG = 'Y-m-d H:i';
    public const DEFAULT_DATE_FORMAT_FULL = 'Y-m-d H:i:s';
    public const DEFAULT_DATE_FORMAT_SHORT = 'Y-m-d';

    /**
     * @var string
     */
    private $dateFormatLong = '';
    /**
     * @var string
     */
    private $dateFormatShort = '';
    /**
     * @var \DateTimeZone
     */
    private $dateTimeZone;

    /**
     * @var \ACP3\Core\Date\DateTranslator
     */
    private $dateTranslator;

    public function __construct(
        DateTranslator $dateTranslator
    ) {
        $this->dateTranslator = $dateTranslator;
    }

    public function getDateFormatLong(): string
    {
        return $this->dateFormatLong;
    }

    /**
     * @return $this
     */
    public function setDateFormatLong(string $dateFormatLong): self
    {
        $this->dateFormatLong = $dateFormatLong;

        return $this;
    }

    public function getDateFormatShort(): string
    {
        return $this->dateFormatShort;
    }

    /**
     * @return $this
     */
    public function setDateFormatShort(string $dateFormatShort): self
    {
        $this->dateFormatShort = $dateFormatShort;

        return $this;
    }

    /**
     * @return $this
     */
    public function setDateTimeZone(\DateTimeZone $dateTimeZone): self
    {
        $this->dateTimeZone = $dateTimeZone;

        return $this;
    }

    /**
     * Gibt ein formatiertes Datum zurück.
     *
     * @throws \Exception
     */
    public function format(string $time = 'now', string $format = 'long', bool $toLocalTimeZone = true, bool $isLocalTimeZone = true): string
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
            $time = date('c', (int) $time);
        }

        $dateTime = new \DateTime($time, $this->dateTimeZone);
        if ($toLocalTimeZone === true) {
            if ($isLocalTimeZone === true) {
                $dateTime->setTimestamp($dateTime->getTimestamp() + $dateTime->getOffset());
            } else {
                $dateTime->setTimestamp($dateTime->getTimestamp() - $dateTime->getOffset());
            }
        }

        return strtr($dateTime->format($format), $this->dateTranslator->localize($format));
    }

    /**
     * Gibt einen einfachen Zeitstempel zurück, welcher sich an UTC ausrichtet.
     *
     * @throws \Exception
     */
    public function timestamp(string $value = 'now', bool $isLocalTime = false): int
    {
        return (int) $this->format($value, 'U', true, $isLocalTime);
    }

    /**
     * Gibt die aktuelle Uhrzeit im MySQL-Datetime Format zurück.
     *
     * @throws \Exception
     */
    public function getCurrentDateTime(bool $isLocalTime = false): string
    {
        return $this->format('now', self::DEFAULT_DATE_FORMAT_FULL, true, $isLocalTime);
    }

    /**
     * Gibt einen an UTC ausgerichteten Zeitstempel im MySQL DateTime Format zurück.
     *
     * @throws \Exception
     */
    public function toSQL(string $value = ''): string
    {
        return $this->format(empty($value) === true ? 'now' : $value, self::DEFAULT_DATE_FORMAT_FULL, true, false);
    }

    /**
     * Konvertiert einen Unix timestamp in das MySQL-Datetime Format.
     *
     * @throws \Exception
     */
    public function timestampToDateTime(string $value, bool $isLocalTime = false): string
    {
        return $this->format($value, self::DEFAULT_DATE_FORMAT_FULL, true, $isLocalTime);
    }

    /**
     * @param string|int $time
     *
     * @throws \Exception
     */
    public function toDateTime($time = 'now'): \DateTime
    {
        if (is_numeric($time)) {
            $time = date('c', $time);
        }

        return new \DateTime($time, $this->dateTimeZone);
    }
}
