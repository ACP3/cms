<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Date;

use ACP3\Core\I18n\Translator;

class DateTranslator
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var array<string, array<string, string>>
     */
    private $cache = [];
    /**
     * @var string[]
     */
    private $daysAbbr = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    /**
     * @var string[]
     */
    private $daysFull = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    /**
     * @var string[]
     */
    private $monthsAbbr = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    /**
     * @var string[]
     */
    private $monthsFull = [
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
        'December',
    ];

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return array<string, string>
     */
    public function localize(string $dateFormat): array
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
     * @param string[] $search
     *
     * @return array<string, string>
     */
    private function cacheLocalizedDate(array $search, string $translatorPrefix): array
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
     * @return array<string, string>
     */
    private function localizeDaysAbbr(): array
    {
        return $this->cacheLocalizedDate($this->daysAbbr, 'day_abbr');
    }

    /**
     * @return array<string, string>
     */
    private function localizeDays(): array
    {
        return $this->cacheLocalizedDate($this->daysFull, 'day_full');
    }

    /**
     * @return array<string, string>
     */
    private function localizeMonthsAbbr(): array
    {
        return $this->cacheLocalizedDate($this->monthsAbbr, 'month_abbr');
    }

    /**
     * @return array<string, string>
     */
    private function localizeMonths(): array
    {
        return $this->cacheLocalizedDate($this->monthsFull, 'month_full');
    }
}
