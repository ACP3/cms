<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\I18n;

use Giggsey\Locale\Locale;

class CountryList
{
    /**
     * @var Translator
     */
    private $translator;
    /**
     * @var null|array
     */
    private $countries = null;
    /**
     * @var null|array
     */
    private $supportedLocales = null;

    /**
     * Country constructor.
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Returns an array with all earth countries
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
            $this->translator->getShortIsoCode()
        ];

        foreach ($locales as $locale) {
            if ($this->isSupportedLocale($locale)) {
                $this->countries = Locale::getAllCountriesForLocale($locale);
            }
        }

        asort($this->countries, SORT_STRING);
    }

    /**
     * @param string $locale
     * @return bool
     */
    private function isSupportedLocale($locale)
    {
        if ($this->supportedLocales === null) {
            $this->supportedLocales = Locale::getSupportedLocales();
        }

        return in_array($locale, $this->supportedLocales);
    }

    /**
     * @return string
     */
    private function getTransformedLocale()
    {
        return strtolower(str_replace('_', '-', $this->translator->getLocale()));
    }
}
