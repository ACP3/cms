<?php
namespace ACP3\Core;

/**
 * Stellt Funktionen bereit, um das ACP3 in verschiedene Sprachen zu übersetzen
 *
 * @author Tino Goratsch
 */
class Lang
{
    /**
     * Die zur Zeit eingestellte Sprache
     *
     * @var string
     */
    protected $lang = '';

    /**
     *
     * @var array
     */
    protected $cache = array();

    function __construct(\ACP3\Core\Auth $auth)
    {
        $lang = $auth->getUserLanguage();
        $this->lang = $this->languagePackExists($lang) === true ? $lang : CONFIG_LANG;
    }

    /**
     * Gibt die aktuell eingestellte Sprache zurück
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->lang;
    }

    /**
     * Verändert die aktuell eingestellte Sprache
     *
     * @param string $lang
     * @return $this
     */
    public function setLanguage($lang)
    {
        if ($this->languagePackExists($lang) === true) {
            $this->lang = $lang;
            $this->cache = array();
        }

        return $this;
    }

    /**
     * Cacht die Sprachfiles, um diese schneller verarbeiten zu können
     */
    public function setLanguageCache()
    {
        $data = array();
        $path = ACP3_ROOT_DIR . 'languages/' . $this->lang . '/';
        $dir = scandir($path);
        foreach ($dir as $module) {
            if ((bool)preg_match('/\.xml$/', $module) === true) {
                $xml = simplexml_load_file($path . $module);
                // Über die einzelnen Sprachstrings iterieren
                foreach ($xml->item as $item) {
                    $data[substr($module, 0, -4)][(string)$item['key']] = (string)$item;
                }
            }
        }

        $this->cache = array();

        return Cache::create($this->lang, $data, 'lang');
    }

    /**
     * Gibt die gecacheten Sprachstrings aus
     *
     * @return array
     */
    protected function getLanguageCache()
    {
        if (Cache::check($this->lang, 'lang') === false) {
            $this->setLanguageCache();
        }

        return Cache::output($this->lang, 'lang');
    }

    /**
     * Gibt den angeforderten Sprachstring aus
     *
     * @param string $module
     * @param string $key
     * @return string
     */
    public function t($module, $key)
    {
        if (empty($this->cache)) {
            $this->cache = $this->getLanguageCache();
        }

        return isset($this->cache[$module][$key]) ? $this->cache[$module][$key] : strtoupper('{' . $module . '_' . $key . '}');
    }

    /**
     * Überprüft, ob das angegebene Sprachpaket existiert
     *
     * @param string $lang
     * @return boolean
     */
    public static function languagePackExists($lang)
    {
        return !preg_match('=/=', $lang) && is_file(ACP3_ROOT_DIR . 'languages/' . $lang . '/info.xml') === true;
    }

    /**
     * Gibt ein Array mit allen Nationen auf der Erde zurück
     */
    public static function worldCountries()
    {
        return array(
            'AU' => 'Australia',
            'AF' => 'Afghanistan',
            'AL' => 'Albania',
            'DZ' => 'Algeria',
            'AS' => 'American Samoa',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AI' => 'Anguilla',
            'AQ' => 'Antarctica',
            'AG' => 'Antigua & Barbuda',
            'AR' => 'Argentina',
            'AM' => 'Armenia',
            'AW' => 'Aruba',
            'AT' => 'Austria',
            'AZ' => 'Azerbaijan',
            'BS' => 'Bahamas',
            'BH' => 'Bahrain',
            'BD' => 'Bangladesh',
            'BB' => 'Barbados',
            'BY' => 'Belarus',
            'BE' => 'Belgium',
            'BZ' => 'Belize',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BT' => 'Bhutan',
            'BO' => 'Bolivia',
            'BA' => 'Bosnia/Hercegovina',
            'BW' => 'Botswana',
            'BV' => 'Bouvet Island',
            'BR' => 'Brazil',
            'IO' => 'British Indian Ocean Territory',
            'BN' => 'Brunei Darussalam',
            'BG' => 'Bulgaria',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'KH' => 'Cambodia',
            'CM' => 'Cameroon',
            'CA' => 'Canada',
            'CV' => 'Cape Verde',
            'KY' => 'Cayman Is',
            'CF' => 'Central African Republic',
            'TD' => 'Chad',
            'CL' => 'Chile',
            'CN' => 'China, People\'s Republic of',
            'CX' => 'Christmas Island',
            'CC' => 'Cocos Islands',
            'CO' => 'Colombia',
            'KM' => 'Comoros',
            'CG' => 'Congo',
            'CD' => 'Congo, Democratic Republic',
            'CK' => 'Cook Islands',
            'CR' => 'Costa Rica',
            'CI' => 'Cote d\'Ivoire',
            'HR' => 'Croatia',
            'CU' => 'Cuba',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DK' => 'Denmark',
            'DJ' => 'Djibouti',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'TP' => 'East Timor',
            'EC' => 'Ecuador',
            'EG' => 'Egypt',
            'SV' => 'El Salvador',
            'GQ' => 'Equatorial Guinea',
            'ER' => 'Eritrea',
            'EE' => 'Estonia',
            'ET' => 'Ethiopia',
            'FK' => 'Falkland Islands',
            'FO' => 'Faroe Islands',
            'FJ' => 'Fiji',
            'FI' => 'Finland',
            'FR' => 'France',
            'FX' => 'France, Metropolitan',
            'GF' => 'French Guiana',
            'PF' => 'French Polynesia',
            'TF' => 'French South Territories',
            'GA' => 'Gabon',
            'GM' => 'Gambia',
            'GE' => 'Georgia',
            'DE' => 'Germany',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GR' => 'Greece',
            'GL' => 'Greenland',
            'GD' => 'Grenada',
            'GP' => 'Guadeloupe',
            'GU' => 'Guam',
            'GT' => 'Guatemala',
            'GN' => 'Guinea',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HT' => 'Haiti',
            'HM' => 'Heard Island And Mcdonald Island',
            'HN' => 'Honduras',
            'HK' => 'Hong Kong',
            'HU' => 'Hungary',
            'IS' => 'Iceland',
            'IN' => 'India',
            'ID' => 'Indonesia',
            'IR' => 'Iran',
            'IQ' => 'Iraq',
            'IE' => 'Ireland',
            'IL' => 'Israel',
            'IT' => 'Italy',
            'JM' => 'Jamaica',
            'JP' => 'Japan',
            'JT' => 'Johnston Island',
            'JO' => 'Jordan',
            'KZ' => 'Kazakhstan',
            'KE' => 'Kenya',
            'KI' => 'Kiribati',
            'KP' => 'Korea, Democratic Peoples Republic',
            'KR' => 'Korea, Republic of',
            'KW' => 'Kuwait',
            'KG' => 'Kyrgyzstan',
            'LA' => 'Lao People\'s Democratic Republic',
            'LV' => 'Latvia',
            'LB' => 'Lebanon',
            'LS' => 'Lesotho',
            'LR' => 'Liberia',
            'LY' => 'Libyan Arab Jamahiriya',
            'LI' => 'Liechtenstein',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'MO' => 'Macau',
            'MK' => 'Macedonia',
            'MG' => 'Madagascar',
            'MW' => 'Malawi',
            'MY' => 'Malaysia',
            'MV' => 'Maldives',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'MH' => 'Marshall Islands',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MU' => 'Mauritius',
            'YT' => 'Mayotte',
            'MX' => 'Mexico',
            'FM' => 'Micronesia',
            'MD' => 'Moldavia',
            'MC' => 'Monaco',
            'MN' => 'Mongolia',
            'MS' => 'Montserrat',
            'MA' => 'Morocco',
            'MZ' => 'Mozambique',
            'MM' => 'Union Of Myanmar',
            'NA' => 'Namibia',
            'NR' => 'Nauru Island',
            'NP' => 'Nepal',
            'NL' => 'Netherlands',
            'AN' => 'Netherlands Antilles',
            'NC' => 'New Caledonia',
            'NZ' => 'New Zealand',
            'NI' => 'Nicaragua',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'NU' => 'Niue',
            'NF' => 'Norfolk Island',
            'MP' => 'Mariana Islands, Northern',
            'NO' => 'Norway',
            'OM' => 'Oman',
            'PK' => 'Pakistan',
            'PW' => 'Palau Islands',
            'PS' => 'Palestine',
            'PA' => 'Panama',
            'PG' => 'Papua New Guinea',
            'PY' => 'Paraguay',
            'PE' => 'Peru',
            'PH' => 'Philippines',
            'PN' => 'Pitcairn',
            'PL' => 'Poland',
            'PT' => 'Portugal',
            'PR' => 'Puerto Rico',
            'QA' => 'Qatar',
            'RE' => 'Reunion Island',
            'RO' => 'Romania',
            'RU' => 'Russian Federation',
            'RW' => 'Rwanda',
            'WS' => 'Samoa',
            'SH' => 'St Helena',
            'KN' => 'St Kitts & Nevis',
            'LC' => 'St Lucia',
            'PM' => 'St Pierre & Miquelon',
            'VC' => 'St Vincent',
            'SM' => 'San Marino',
            'ST' => 'Sao Tome & Principe',
            'SA' => 'Saudi Arabia',
            'SN' => 'Senegal',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SG' => 'Singapore',
            'SK' => 'Slovakia',
            'SI' => 'Slovenia',
            'SB' => 'Solomon Islands',
            'SO' => 'Somalia',
            'ZA' => 'South Africa',
            'GS' => 'South Georgia and South Sandwich',
            'ES' => 'Spain',
            'LK' => 'Sri Lanka',
            'XX' => 'Stateless Persons',
            'SD' => 'Sudan',
            'SR' => 'Suriname',
            'SJ' => 'Svalbard and Jan Mayen',
            'SZ' => 'Swaziland',
            'SE' => 'Sweden',
            'CH' => 'Switzerland',
            'SY' => 'Syrian Arab Republic',
            'TW' => 'Taiwan, Republic of China',
            'TJ' => 'Tajikistan',
            'TZ' => 'Tanzania',
            'TH' => 'Thailand',
            'TL' => 'Timor Leste',
            'TG' => 'Togo',
            'TK' => 'Tokelau',
            'TO' => 'Tonga',
            'TT' => 'Trinidad & Tobago',
            'TN' => 'Tunisia',
            'TR' => 'Turkey',
            'TM' => 'Turkmenistan',
            'TC' => 'Turks And Caicos Islands',
            'TV' => 'Tuvalu',
            'UG' => 'Uganda',
            'UA' => 'Ukraine',
            'AE' => 'United Arab Emirates',
            'GB' => 'United Kingdom',
            'UM' => 'US Minor Outlying Islands',
            'US' => 'USA',
            'HV' => 'Upper Volta',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VU' => 'Vanuatu',
            'VA' => 'Vatican City State',
            'VE' => 'Venezuela',
            'VN' => 'Vietnam',
            'VG' => 'Virgin Islands (British)',
            'VI' => 'Virgin Islands (US)',
            'WF' => 'Wallis And Futuna Islands',
            'EH' => 'Western Sahara',
            'YE' => 'Yemen Arab Rep.',
            'YD' => 'Yemen Democratic',
            'YU' => 'Yugoslavia',
            'ZR' => 'Zaire',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe'
        );
    }

    /**
     * Parst den ACCEPT-LANGUAGE Header des Browsers
     * und selektiert die präferierte Sprache
     *
     * @return string
     */
    final public static function parseAcceptLanguage()
    {
        $langs = array();

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $matches = array();
            preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);

            if (!empty($matches[1])) {
                $langs = array_combine($matches[1], $matches[4]);

                // Für Einträge ohne q-Faktor, Wert auf 1 setzen
                foreach ($langs as $lang => $val) {
                    if ($val === '')
                        $langs[$lang] = 1;
                }

                // Liste nach Sprachpräferenz sortieren
                arsort($langs, SORT_NUMERIC);
            }
        }

        // Über die Sprachen iterieren und das passende Sprachpaket auswählen
        foreach ($langs as $lang => $val) {
            if (self::languagePackExists($lang) === true) {
                return $lang;
            }
        }
        return 'en';
    }
}