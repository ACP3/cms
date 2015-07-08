<?php
namespace ACP3\Installer\Core;

/**
 * Class Date
 * @package ACP3\Installer\Core
 */
class Date extends \ACP3\Core\Date
{
    /**
     * @param \ACP3\Installer\Core\Lang $lang
     */
    public function __construct(
        Lang $lang
    )
    {
        $this->lang = $lang;

        $defaultTimeZone = date_default_timezone_get();

        $settings = [
            'date_format_long' => 'd.m.y, H:i',
            'date_format_short' => 'd.m.y',
            'time_zone' => !empty($defaultTimeZone) ? $defaultTimeZone : 'UTC',
        ];
        $this->_setFormatAndTimeZone($settings);
    }
}
