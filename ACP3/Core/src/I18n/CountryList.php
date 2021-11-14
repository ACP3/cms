<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\I18n;

use Giggsey\Locale\Locale;

class CountryList
{
    /**
     * @var array|null
     */
    private $countries;
    /**
     * @var array|null
     */
    private $supportedLocales;

    public function __construct(private Translator $translator)
    {
    }

    /**
     * Returns an array with all earth countries.
     */
    public function worldCountries(): array
    {
        if ($this->countries === null) {
            $this->cacheWorldCountries();
        }

        return $this->countries;
    }

    private function cacheWorldCountries(): void
    {
        $this->countries = [];

        $locales = [
            $this->getTransformedLocale(),
            $this->translator->getShortIsoCode(),
        ];

        foreach ($locales as $locale) {
            if ($this->isSupportedLocale($locale)) {
                $this->countries = Locale::getAllCountriesForLocale($locale);
            }
        }

        asort($this->countries, SORT_STRING);
    }

    private function isSupportedLocale(string $locale): bool
    {
        if ($this->supportedLocales === null) {
            $this->supportedLocales = Locale::getSupportedLocales();
        }

        return \in_array($locale, $this->supportedLocales, true);
    }

    private function getTransformedLocale(): string
    {
        return strtolower(str_replace('_', '-', $this->translator->getLocale()));
    }
}
