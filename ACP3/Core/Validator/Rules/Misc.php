<?php

namespace ACP3\Core\Validator\Rules;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\SessionHandler;

/**
 * Class Misc
 * @package ACP3\Core
 */
class Misc
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\SessionHandler
     */
    protected $sessionHandler;

    /**
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\SessionHandler   $sessionHandler
     */
    public function __construct(
        RequestInterface $request,
        SessionHandler $sessionHandler
    )
    {
        $this->request = $request;
        $this->sessionHandler = $sessionHandler;
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
        $tokenName = SessionHandler::XSRF_TOKEN_NAME;
        $urlQueryString = $this->request->getQuery();
        $sessionToken = $this->sessionHandler->get($tokenName);

        return (isset($sessionToken[$urlQueryString]) && $this->request->getPost()->get($tokenName, '') === $sessionToken[$urlQueryString]);
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
