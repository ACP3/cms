<?php
/**
 * Copyright (c) by the ACP3 Developers.
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
     * @var null|array
     */
    private $countries = null;

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
        $basePath = ACP3_ROOT_DIR . 'vendor/giggsey/locale/data/';
        $supportedLocales = include $basePath . '_list.php';

        $this->countries = [];
        if ($this->isSupportedLocale($supportedLocales)) {
            $paths = [
                $basePath . $this->getTransformedLocale() . '.php',
                $basePath . $this->translator->getShortIsoCode() . '.php'
            ];
            foreach ($paths as $path) {
                if (is_file($path)) {
                    $this->countries = include $path;
                    break;
                }
            }

            asort($this->countries, SORT_STRING);
        }
    }

    /**
     * @param array $supportedLocales
     * @return bool
     */
    private function isSupportedLocale(array $supportedLocales)
    {
        $localeAndRegion = $this->getTransformedLocale();

        return array_key_exists($localeAndRegion, $supportedLocales)
            || array_key_exists($this->translator->getShortIsoCode(), $supportedLocales);
    }

    /**
     * @return string
     */
    private function getTransformedLocale()
    {
        return strtolower(str_replace('_', '-', $this->translator->getLocale()));
    }
}
