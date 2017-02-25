<?php
namespace ACP3\Modules\ACP3\Users\Helpers;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\CountryList;
use ACP3\Core\I18n\Translator;

class Forms
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelpers;
    /**
     * @var CountryList
     */
    private $country;

    /**
     * @param \ACP3\Core\I18n\Translator $translator
     * @param CountryList $countryList
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\Helpers\Forms $formsHelpers
     */
    public function __construct(
        Translator $translator,
        CountryList $countryList,
        RequestInterface $request,
        \ACP3\Core\Helpers\Forms $formsHelpers
    ) {
        $this->translator = $translator;
        $this->request = $request;
        $this->formsHelpers = $formsHelpers;
        $this->country = $countryList;
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
    ) {
        return [
            [
                'name' => 'mail',
                'lang' => $this->translator->t('system', 'email_address'),
                'value' => $this->request->getPost()->get('mail', $defaultMail),
                'maxlength' => '120',
            ],
            [
                'name' => 'website',
                'lang' => $this->translator->t('system', 'website'),
                'value' => $this->request->getPost()->get('website', $defaultWebsite),
                'maxlength' => '120',
            ],
            [
                'name' => 'icq',
                'lang' => $this->translator->t('users', 'icq'),
                'value' => $this->request->getPost()->get('icq', $defaultIcqNumber),
                'maxlength' => '9',
            ],
            [
                'name' => 'skype',
                'lang' => $this->translator->t('users', 'skype'),
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
        return $this->formsHelpers->choicesGenerator(
            'country',
            $this->country->worldCountries(),
            $defaultValue
        );
    }

    /**
     * @param int $displayAddress
     * @param int $displayBirthday
     * @param int $displayCountry
     * @param int $displayMail
     * @return array
     */
    public function fetchUserSettingsFormFields(
        $displayAddress = 0,
        $displayBirthday = 0,
        $displayCountry = 0,
        $displayMail = 0
    ) {
        return [
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
        return $this->formsHelpers->yesNoCheckboxGenerator('address_display', $value);
    }

    /**
     * @param int $value
     *
     * @return array
     */
    protected function displayBirthday($value)
    {
        $displayBirthday = [
            0 => $this->translator->t('users', 'birthday_hide'),
            1 => $this->translator->t('users', 'birthday_display_completely'),
            2 => $this->translator->t('users', 'birthday_hide_year')
        ];
        return $this->formsHelpers->checkboxGenerator('birthday_display', $displayBirthday, $value);
    }

    /**
     * @param int $value
     *
     * @return array
     */
    protected function displayCountry($value)
    {
        return $this->formsHelpers->yesNoCheckboxGenerator('country_display', $value);
    }

    /**
     * @param int $value
     *
     * @return array
     */
    protected function displayMail($value)
    {
        return $this->formsHelpers->yesNoCheckboxGenerator('mail_display', $value);
    }

    /**
     * @param string $birthday
     * @param string $country
     * @param int    $gender
     *
     * @return array
     */
    public function fetchUserProfileFormFields($birthday = '', $country = '', $gender = 1)
    {
        return [
            'birthday' => $birthday,
            'countries' => $this->generateWorldCountriesSelect($country),
            'gender' => $this->fetchGenderField($gender),
        ];
    }

    /**
     * @param int $currentGender
     *
     * @return array
     */
    protected function fetchGenderField($currentGender)
    {
        $genders = [
            1 => $this->translator->t('users', 'gender_not_specified'),
            2 => $this->translator->t('users', 'gender_female'),
            3 => $this->translator->t('users', 'gender_male')
        ];
        return $this->formsHelpers->choicesGenerator('gender', $genders, $currentGender);
    }
}
