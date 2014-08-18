<?php

namespace ACP3\Core\Validator\Rules;

use ACP3\Core\Request;

/**
 * Class Misc
 * @package ACP3\Core
 */
class Misc
{
    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
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
     */
    public function email($var)
    {
        if (function_exists('filter_var')) {
            return (bool)filter_var($var, FILTER_VALIDATE_EMAIL);
        } else {
            $pattern = '/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/';
            return (bool)preg_match($pattern, $var);
        }
    }

    /**
     * Validiert das Formtoken auf seine Gültigkeit
     *
     * @return boolean
     */
    public function formToken()
    {
        $tokenName = \ACP3\Core\Session::XSRF_TOKEN_NAME;
        $urlQueryString = $this->request->query;

        return (isset($_POST[$tokenName]) && isset($_SESSION[$tokenName][$urlQueryString]) && $_POST[$tokenName] === $_SESSION[$tokenName][$urlQueryString]);
    }

    /**
     * Überprüft, ob ein gültiger MD5-Hash übergeben wurde
     *
     * @param string $string
     *
     * @return boolean
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
     */
    public function isNumber($var)
    {
        return (bool)preg_match('/^(\d+)$/', $var);
    }

}