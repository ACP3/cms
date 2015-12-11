<?php
namespace ACP3\Installer\Core;

use ACP3\Installer\Core\I18n\Translator;

/**
 * Class Date
 * @package ACP3\Installer\Core
 */
class Date extends \ACP3\Core\Date
{
    /**
     * @param \ACP3\Installer\Core\I18n\Translator $translator
     */
    public function __construct(
        Translator $translator
    )
    {
        $this->translator = $translator;

        $defaultTimeZone = date_default_timezone_get();

        $settings = [
            'date_format_long' => 'd.m.y, H:i',
            'date_format_short' => 'd.m.y',
            'time_zone' => !empty($defaultTimeZone) ? $defaultTimeZone : 'UTC',
        ];
        $this->_setFormatAndTimeZone($settings);
    }
}
