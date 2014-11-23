<?php
namespace ACP3\Core\Helpers;

use ACP3\Core;

/**
 * Class Secure
 * @package ACP3\Core\Helpers
 */
class Secure
{
    /**
     * @var Core\View
     */
    protected $view;

    /**
     * @param Core\View $view
     */
    public function __construct(Core\View $view)
    {
        $this->view = $view;
    }

    /**
     * Generiert ein gesalzenes Passwort
     *
     * @param string $salt
     *    Das zu verwendende Salz
     * @param string $plaintext
     *    Das Passwort in Klartextform, welches verschlüsselt werden soll
     * @param string $algorithm
     *    Der zu verwendende Hash-Algorithmus
     *
     * @return string
     */
    public function generateSaltedPassword($salt, $plaintext, $algorithm = 'sha1')
    {
        return hash($algorithm, $salt . hash($algorithm, $plaintext));
    }

    /**
     * Generiert einen Zufallsstring beliebiger Länge
     *
     * @param integer $strLength
     *  Länge des zufälligen Strings
     *
     * @return string
     */
    public function salt($strLength)
    {
        $salt = '';
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $c_chars = strlen($chars) - 1;
        while (strlen($salt) < $strLength) {
            $char = $chars[mt_rand(0, $c_chars)];
            // Zeichen nur hinzufügen, wenn sich dieses nicht bereits im Salz befindet
            if (strpos($salt, $char) === false) {
                $salt .= $char;
            }
        }
        return $salt;
    }

    /**
     * Enkodiert alle HTML-Entitäten eines Strings
     * zur Vermeidung von XSS
     *
     * @param string  $var
     * @param boolean $scriptTagOnly
     *
     * @return string
     */
    public function strEncode($var, $scriptTagOnly = false)
    {
        $var = preg_replace('=<script[^>]*>.*</script>=isU', '', $var);
        return $scriptTagOnly === true ? $var : htmlentities($var, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generiert für ein Formular ein Securitytoken
     *
     * @param string $path
     *    Optionaler ACP3 interner URI Pfad, für welchen das Token gelten soll
     */
    public function generateFormToken($path)
    {
        $tokenName = Core\Session::XSRF_TOKEN_NAME;
        if (!isset($_SESSION[$tokenName]) || is_array($_SESSION[$tokenName]) === false) {
            $_SESSION[$tokenName] = [];
        }

        $path = $path . (!preg_match('/\/$/', $path) ? '/' : '');

        if (empty($_SESSION[$tokenName][$path])) {
            $_SESSION[$tokenName][$path] = sha1(uniqid(mt_rand(), true));
        }

        $this->view->assign('form_token', '<input type="hidden" name="' . $tokenName . '" value="' . $_SESSION[$tokenName][$path] . '" />');
    }

    /**
     * Entfernt das Securitytoken aus der Session
     *
     * @param string $path
     * @param string $token
     */
    public function unsetFormToken($path, $token = '')
    {
        $tokenName = Core\Session::XSRF_TOKEN_NAME;
        if (empty($token) && isset($_POST[$tokenName])) {
            $token = $_POST[$tokenName];
        }
        if (!empty($token) && is_array($_SESSION[$tokenName]) === true) {
            if (isset($_SESSION[$tokenName][$path])) {
                unset($_SESSION[$tokenName][$path]);
            }
        }
    }
}