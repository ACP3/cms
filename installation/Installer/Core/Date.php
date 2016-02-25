<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Core;

use ACP3\Core\Date\DateTranslator;

/**
 * Class Date
 * @package ACP3\Installer\Core
 */
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

        $defaultTimeZone = date_default_timezone_get();

        $settings = [
            'date_format_long' => 'd.m.y, H:i',
            'date_format_short' => 'd.m.y',
            'time_zone' => !empty($defaultTimeZone) ? $defaultTimeZone : 'UTC',
        ];
        $this->setFormatAndTimeZone($settings);
    }
}
