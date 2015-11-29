<?php
namespace ACP3\Modules\ACP3\Users\Validator;

use ACP3\Core;

/**
 * Class AbstractUserValidator
 * @package ACP3\Modules\ACP3\Users\Validator
 */
abstract class AbstractUserValidator extends Core\Validator\AbstractValidator
{
    /**
     * @var \ACP3\Core\Validator\Rules\Date
     */
    protected $dateValidator;

    /**
     * AbstractUserValidator constructor.
     *
     * @param \ACP3\Core\Lang                 $lang
     * @param \ACP3\Core\Validator\Validator  $validator
     * @param \ACP3\Core\Validator\Rules\Misc $validate
     * @param \ACP3\Core\Validator\Rules\Date $dateValidator
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Validator $validator,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Date $dateValidator
    )
    {
        parent::__construct($lang, $validator, $validate);

        $this->dateValidator = $dateValidator;
    }

    /**
     * @param array  $formData
     * @param string $passwordField
     * @param string $passwordConfirmationField
     */
    protected function validateNewPassword(array $formData, $passwordField, $passwordConfirmationField)
    {
        if (!empty($formData[$passwordField]) &&
            !empty($formData[$passwordConfirmationField]) &&
            $formData[$passwordField] !== $formData[$passwordConfirmationField]
        ) {
            $this->errors[str_replace('_', '-', $passwordField)] = $this->lang->t('users', 'type_in_pwd');
        }
    }

    /**
     * @param array  $formData
     * @param string $passwordField
     * @param string $passwordConfirmationField
     */
    protected function validatePassword(array $formData, $passwordField, $passwordConfirmationField)
    {
        if (empty($formData[$passwordField]) ||
            empty($formData[$passwordConfirmationField]) ||
            $formData[$passwordField] !== $formData[$passwordConfirmationField]
        ) {
            $this->errors[str_replace('_', '-', $passwordField)] = $this->lang->t('users', 'type_in_pwd');
        }
    }

    /**
     * Bestimmung des Geschlechts
     *  1 = Keine Angabe
     *  2 = Weiblich
     *  3 = Männlich
     *
     * @param string , integer $var
     *               Die zu überprüfende Variable
     *
     * @return boolean
     */
    protected function _gender($var)
    {
        return in_array($var, [1, 2, 3]);
    }

    /**
     * Überprüft, ob eine gültige ICQ-Nummer eingegeben wurde
     *
     * @param integer $var
     *
     * @return boolean
     */
    protected function _icq($var)
    {
        return (bool)preg_match('/^(\d{6,9})$/', $var);
    }

    /**
     * @param array $formData
     * @param int   $languageOverride
     * @param int   $entriesOverride
     */
    protected function validateUserSettings(array $formData, $languageOverride = 1, $entriesOverride = 1)
    {
        if ($languageOverride == 1 && $this->lang->languagePackExists($formData['language']) === false) {
            $this->errors['language'] = $this->lang->t('users', 'select_language');
        }
        if ($entriesOverride == 1 && $this->validate->isNumber($formData['entries']) === false) {
            $this->errors['entries'] = $this->lang->t('system', 'select_records_per_page');
        }
        if (empty($formData['date_format_long'])) {
            $this->errors['date-format-long'] = $this->lang->t('system', 'type_in_long_date_format');
        }
        if (empty($formData['date_format_short'])) {
            $this->errors['date-format-short'] = $this->lang->t('system', 'type_in_short_date_format');
        }
        if ($this->dateValidator->timeZone($formData['date_time_zone']) === false) {
            $this->errors['time-zone'] = $this->lang->t('system', 'select_time_zone');
        }
        if (in_array($formData['mail_display'], [0, 1]) === false) {
            $this->errors['mail-display'] = $this->lang->t('users', 'select_mail_display');
        }
        if (in_array($formData['address_display'], [0, 1]) === false) {
            $this->errors['address-display'] = $this->lang->t('users', 'select_address_display');
        }
        if (in_array($formData['country_display'], [0, 1]) === false) {
            $this->errors['country-display'] = $this->lang->t('users', 'select_country_display');
        }
        if (in_array($formData['birthday_display'], [0, 1, 2]) === false) {
            $this->errors['birthday-display'] = $this->lang->t('users', 'select_birthday_display');
        }
    }
}