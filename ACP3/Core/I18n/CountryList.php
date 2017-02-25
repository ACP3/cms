<?php
/**
 * Copyright (c) 2017 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\I18n;

class CountryList
{
    /**
     * @var Translator
     */
    private $translator;

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
        $path = ACP3_ROOT_DIR . 'vendor/umpirsky/country-list/data/' . $this->translator->getLocale() . '/country.json';

        if (preg_match('/^[a-z]{2}_[A-Z]{2}/', $this->translator->getLocale()) && is_file($path)) {
            $countries = file_get_contents($path);

            return json_decode($countries, true);
        }

        return [];
    }
}
