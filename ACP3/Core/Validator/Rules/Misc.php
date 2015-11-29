<?php

namespace ACP3\Core\Validator\Rules;

use ACP3\Core\Validator\ValidationRules\EmailValidationRule;
use ACP3\Core\Validator\ValidationRules\FormTokenValidationRule;
use ACP3\Core\Validator\ValidationRules\IntegerValidationRule;

/**
 * Class Misc
 * @package ACP3\Core
 *
 * @deprecated
 */
class Misc
{
    /**
     * @var \ACP3\Core\Validator\ValidationRules\EmailValidationRule
     */
    protected $emailValidationRule;
    /**
     * @var \ACP3\Core\Validator\ValidationRules\FormTokenValidationRule
     */
    protected $formTokenValidationRule;
    /**
     * @var \ACP3\Core\Validator\ValidationRules\IntegerValidationRule
     */
    protected $integerValidationRule;

    /**
     * Misc constructor.
     *
     * @param \ACP3\Core\Validator\ValidationRules\EmailValidationRule     $emailValidationRule
     * @param \ACP3\Core\Validator\ValidationRules\FormTokenValidationRule $formTokenValidationRule
     * @param \ACP3\Core\Validator\ValidationRules\IntegerValidationRule   $integerValidationRule
     */
    public function __construct(
        EmailValidationRule $emailValidationRule,
        FormTokenValidationRule $formTokenValidationRule,
        IntegerValidationRule $integerValidationRule
    )
    {
        $this->emailValidationRule = $emailValidationRule;
        $this->formTokenValidationRule = $formTokenValidationRule;
        $this->integerValidationRule = $integerValidationRule;
    }

    /**
     * Überprüft, ob eine standardkonforme E-Mail-Adresse übergeben wurde
     *
     * @copyright HTML/QuickForm/Rule/Email.php
     *    Suchmuster von PEAR entnommen
     *
     * @param string $var
     *  Zu überprüfende E-Mail-Adresse
     *
     * @return boolean
     *
     * @deprecated
     */
    public function email($var)
    {
        return $this->emailValidationRule->isValid($var);
    }

    /**
     * Validiert das Formtoken auf seine Gültigkeit
     *
     * @return boolean
     *
     * @deprecated
     */
    public function formToken()
    {
        return $this->formTokenValidationRule->isValid('');
    }

    /**
     * Überprüft, ob ein gültiger MD5-Hash übergeben wurde
     *
     * @param string $string
     *
     * @return boolean
     *
     * @deprecated
     */
    public function isMD5($string)
    {
        return is_string($string) === true && preg_match('/^[a-f\d]+$/', $string) && strlen($string) === 32;
    }

    /**
     * Überprüft eine Variable, ob diese nur aus Ziffern besteht
     *
     * @param mixed $var
     *
     * @return boolean
     *
     * @deprecated
     */
    public function isNumber($var)
    {
        return $this->integerValidationRule->isValid($var);
    }
}
