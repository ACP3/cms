<?php
namespace ACP3\Modules\ACP3\Users\Helpers;

use ACP3\Core\Helpers\Country;
use ACP3\Core\Helpers\Date;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Lang;

/**
 * Class Forms
 * @package ACP3\Modules\ACP3\Users\Helpers
 */
class Forms
{
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\Helpers\Date
     */
    protected $dateHelpers;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelpers;

    /**
     * @param \ACP3\Core\Lang                  $lang
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\Helpers\Date          $dateHelpers
     * @param \ACP3\Core\Helpers\Forms         $formsHelpers
     */
    public function __construct(
        Lang $lang,
        RequestInterface $request,
        Date $dateHelpers,
        \ACP3\Core\Helpers\Forms $formsHelpers
    )
    {
        $this->lang = $lang;
        $this->request = $request;
        $this->dateHelpers = $dateHelpers;
        $this->formsHelpers = $formsHelpers;
    }

    /**
     * @param string $defaultMail
     * @param string $defaultWebsite
     * @param string $defaultIcqNumber
     * @param string $defaultSkypeName
     *
     * @return array
     */
    public function fetchContactDetails(
        $defaultMail = '',
        $defaultWebsite = '',
        $defaultIcqNumber = '',
        $defaultSkypeName = ''
    )
    {
        return [
            [
                'name' => 'mail',
                'lang' => $this->lang->t('system', 'email_address'),
                'value' => $this->request->getPost()->get('mail', $defaultMail),
                'maxlength' => '120',
            ],
            [
                'name' => 'website',
                'lang' => $this->lang->t('system', 'website'),
                'value' => $this->request->getPost()->get('website', $defaultWebsite),
                'maxlength' => '120',
            ],
            [
                'name' => 'icq',
                'lang' => $this->lang->t('users', 'icq'),
                'value' => $this->request->getPost()->get('icq', $defaultIcqNumber),
                'maxlength' => '9',
            ],
            [
                'name' => 'skype',
                'lang' => $this->lang->t('users', 'skype'),
                'value' => $this->request->getPost()->get('skype', $defaultSkypeName),
                'maxlength' => '28',
            ]
        ];
    }

    /**
     * @param string $defaultValue
     *
     * @return array
     */
    public function generateWorldCountriesSelect($defaultValue = '')
    {
        $countries = Country::worldCountries();
        $countriesSelect = [];
        foreach ($countries as $key => $value) {
            $countriesSelect[] = [
                'value' => $key,
                'lang' => $value,
                'selected' => $this->formsHelpers->selectEntry('countries', $key, $defaultValue),
            ];
        }
        return $countriesSelect;
    }

    public function fetchUserSettingsFormFields(
        $entries,
        $language,
        $timeZone,
        $displayAddress = 0,
        $displayBirthday = 0,
        $displayCountry = 0,
        $displayMail = 0
    )
    {
        return [
            'entries' => $this->formsHelpers->recordsPerPage((int)$entries),
            'languages' => $this->lang->getLanguagePack($this->request->getPost()->get('language', $language)),
            'time_zones' => $this->dateHelpers->getTimeZones($timeZone),
            'address_display' => $this->displayAddress($displayAddress),
            'birthday_display' => $this->displayBirthday($displayBirthday),
            'country_display' => $this->displayCountry($displayCountry),
            'mail_display' => $this->displayMail($displayMail),
        ];
    }

    /**
     * @param int $value
     *
     * @return array
     */
    protected function displayAddress($value)
    {
        $lang_addressDisplay = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        return $this->formsHelpers->checkboxGenerator('address_display', [1, 0], $lang_addressDisplay, $value);
    }

    /**
     * @param int $value
     *
     * @return array
     */
    protected function displayBirthday($value)
    {
        $lang_birthdayDisplay = [
            $this->lang->t('users', 'birthday_hide'),
            $this->lang->t('users', 'birthday_display_completely'),
            $this->lang->t('users', 'birthday_hide_year')
        ];
        return $this->formsHelpers->checkboxGenerator('birthday_display', [0, 1, 2], $lang_birthdayDisplay, $value);
    }

    /**
     * @param int $value
     *
     * @return array
     */
    protected function displayCountry($value)
    {
        $lang_countryDisplay = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        return $this->formsHelpers->checkboxGenerator('country_display', [1, 0], $lang_countryDisplay, $value);
    }

    /**
     * @param int $value
     *
     * @return array
     */
    protected function displayMail($value)
    {
        $lang_mailDisplay = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        return $this->formsHelpers->checkboxGenerator('mail_display', [1, 0], $lang_mailDisplay, $value);
    }

    /**
     * @param string $birthday
     * @param string $country
     * @param int $gender
     *
     * @return array
     */
    public function fetchUserProfileFormFields($birthday = '', $country = '', $gender = 1)
    {
        return [
            'birthday_datepicker' => $this->fetchBirthdayDatepicker($birthday),
            'countries' => $this->generateWorldCountriesSelect($country),
            'gender' => $this->fetchGenderField($gender),
        ];
    }

    /**
     * @param int $value
     *
     * @return array
     */
    protected function fetchGenderField($value)
    {
        $lang_gender = [
            $this->lang->t('users', 'gender_not_specified'),
            $this->lang->t('users', 'gender_female'),
            $this->lang->t('users', 'gender_male')
        ];
        return $this->formsHelpers->selectGenerator('gender', [1, 2, 3], $lang_gender, $value);
    }

    /**
     * @param string $value
     *
     * @return array
     */
    protected function fetchBirthdayDatepicker($value)
    {
        $datepickerParams = [
            'constrainInput' => 'true',
            'changeMonth' => 'true',
            'changeYear' => 'true',
            'yearRange' => "'-50:+0'"
        ];
        return $this->dateHelpers->datepicker('birthday', $value, 'Y-m-d', $datepickerParams, false, true);
    }

}