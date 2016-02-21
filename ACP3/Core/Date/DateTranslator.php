<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing
 * details.
 */

namespace ACP3\Core\Date;

use ACP3\Core\I18n\Translator;

/**
 * Class DateTranslator
 * @package ACP3\Core\Date
 */
class DateTranslator
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var array
     */
    protected $cache = [];
    /**
     * @var array
     */
    protected $daysAbbr = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    /**
     * @var array
     */
    protected $daysFull = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    /**
     * @var array
     */
    protected $monthsAbbr = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    /**
     * @var array
     */
    protected $monthsFull = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
    ];

    /**
     * DateTranslator constructor.
     *
     * @param \ACP3\Core\I18n\Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param string $dateFormat
     *
     * @return array
     */
    public function localize($dateFormat)
    {
        $replace = [];
        // Localize days
        if (strpos($dateFormat, 'D') !== false) {
            $replace = $this->localizeDaysAbbr();
        } elseif (strpos($dateFormat, 'l') !== false) {
            $replace = $this->localizeDays();
        }

        // Localize months
        if (strpos($dateFormat, 'M') !== false) {
            $replace = array_merge($replace, $this->localizeMonthsAbbr());
        } elseif (strpos($dateFormat, 'F') !== false) {
            $replace = array_merge($replace, $this->localizeMonths());
        }

        return $replace;
    }

    /**
     * @param array  $search
     * @param string $translatorPrefix
     */
    protected function cacheLocalizedDate(array $search, $translatorPrefix)
    {
        if (!isset($this->cache[$translatorPrefix])) {
            $buffer = [];
            foreach ($search as $key) {
                $buffer[$key] = $this->translator->t('system', $translatorPrefix . '_' . strtolower($key));
            }
            $this->cache[$translatorPrefix] = $buffer;
        }

        return $this->cache[$translatorPrefix];
    }

    /**
     * @return array
     */
    protected function localizeDaysAbbr()
    {
        return $this->cacheLocalizedDate($this->daysAbbr, 'day_abbr');
    }

    /**
     * @return array
     */
    protected function localizeDays()
    {
        return $this->cacheLocalizedDate($this->daysFull, 'day_full');
    }

    /**
     * @return array
     */
    protected function localizeMonthsAbbr()
    {
        return $this->cacheLocalizedDate($this->monthsAbbr, 'month_abbr');
    }

    /**
     * @return array
     */
    protected function localizeMonths()
    {
        return $this->cacheLocalizedDate($this->monthsFull, 'month_full');
    }
}