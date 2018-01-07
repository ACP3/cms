<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\I18n;

use Giggsey\Locale\Locale as LocaleLib;

class CountryList
{
    /**
     * @var LocaleInterface
     */
    private $locale;
    /**
     * @var null|array
     */
    private $countries;
    /**
     * @var null|array
     */
    private $supportedLocales;

    /**
     * Country constructor.
     *
     * @param LocaleInterface $locale
     */
    public function __construct(LocaleInterface $locale)
    {
        $this->locale = $locale;
    }

    /**
     * Returns an array with all earth countries.
     *
     * @return array
     */
    public function worldCountries()
    {
        if ($this->countries === null) {
            $this->cacheWorldCountries();
        }

        return $this->countries;
    }

    private function cacheWorldCountries()
    {
        $this->countries = [];

        $locales = [
            $this->getTransformedLocale(),
            $this->locale->getShortIsoCode(),
        ];

        foreach ($locales as $locale) {
            if ($this->isSupportedLocale($locale)) {
                $this->countries = LocaleLib::getAllCountriesForLocale($locale);
            }
        }

        \asort($this->countries, SORT_STRING);
    }

    /**
     * @param string $locale
     *
     * @return bool
     */
    private function isSupportedLocale($locale)
    {
        if ($this->supportedLocales === null) {
            $this->supportedLocales = LocaleLib::getSupportedLocales();
        }

        return \in_array($locale, $this->supportedLocales);
    }

    /**
     * @return string
     */
    private function getTransformedLocale()
    {
        return \strtolower(\str_replace('_', '-', $this->locale->getLocale()));
    }
}
