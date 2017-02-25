<?php
namespace ACP3\Core\Helpers;

class Country
{
    /**
     * Returns an array with all earth countries
     *
     * @param string $locale
     * @return array
     */
    public static function worldCountries($locale = 'en_US')
    {
        $path = ACP3_ROOT_DIR . 'vendor/umpirsky/country-list/data/' . $locale . '/country.json';

        if (preg_match('/^[a-z]{2}_[A-Z]{2}/', $locale) && is_file($path)) {
            $countries = file_get_contents($path);

            return json_decode($countries, true);
        }

        return [];
    }
}
