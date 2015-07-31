<?php
namespace ACP3\Modules\ACP3\Users\Helpers;

use ACP3\Core\Helpers\Country;
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
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelpers;

    /**
     * @param \ACP3\Core\Lang                  $lang
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\Helpers\Forms         $formsHelpers
     */
    public function __construct(
        Lang $lang,
        RequestInterface $request,
        \ACP3\Core\Helpers\Forms $formsHelpers
    )
    {
        $this->lang = $lang;
        $this->request = $request;
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


}