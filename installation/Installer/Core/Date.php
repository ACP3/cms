<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Core;

use ACP3\Core\Date\DateTranslator;

class Date extends \ACP3\Core\Date
{
    /**
     * Date constructor.
     *
     * @param \ACP3\Core\Date\DateTranslator $dateTranslator
     */
    public function __construct(
        DateTranslator $dateTranslator
    ) {
        $this->dateTranslator = $dateTranslator;

        $this->setFormatAndTimeZone();
    }

    protected function setFormatAndTimeZone()
    {
        $defaultTimeZone = date_default_timezone_get();

        $this->dateFormatLong = 'd.m.y, H:i';
        $this->dateFormatShort = 'd.m.y';
        $timeZone = !empty($defaultTimeZone) ? $defaultTimeZone : 'UTC';
        $this->dateTimeZone = new \DateTimeZone($timeZone);
    }
}
