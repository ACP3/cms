<?php
namespace ACP3\Installer\Core;

use ACP3\Core\Helpers\Forms;

/**
 * Class Date
 * @package ACP3\Installer\Core
 */
class Date extends \ACP3\Core\Date
{
    /**
     * @param Lang $lang
     * @param Forms $formsHelper
     * @param \ACP3\Core\Validator\Rules\Date $dateValidator
     */
    public function __construct(
        Lang $lang,
        Forms $formsHelper,
        \ACP3\Core\Validator\Rules\Date $dateValidator
    ) {
        $this->lang = $lang;
        $this->formsHelper = $formsHelper;
        $this->dateValidator = $dateValidator;

        $defaultTimeZone = date_default_timezone_get();

        $settings = [
            'date_format_long' => 'd.m.y, H:i',
            'date_format_short' => 'd.m.y',
            'time_zone' => !empty($defaultTimeZone) ? $defaultTimeZone : 'UTC',
        ];
        $this->_setFormatAndTimeZone($settings);
    }
}
