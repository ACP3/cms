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
        return $var == 1 || $var == 2 || $var == 3;
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
}