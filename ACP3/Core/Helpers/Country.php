<?php
namespace ACP3\Core\Helpers;

/**
 * Class Country
 * @package ACP3\Core\Helpers
 */
class Country
{
    /**
     * Returns an array with all countries on earth
     *
     * @param string $locale
     * @return array
     */
    public static function worldCountries($locale = 'en_US')
    {
        $countries = file_get_contents(
            ACP3_ROOT_DIR . 'vendor/umpirsky/country-list/data/' . $locale . '/country.json'
        );

        return json_decode($countries, true);
    }
}
