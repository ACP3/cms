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
     * Generiert ein gesalzenes Passwort
     *
     * @param string $salt
     * @param string $password
     * @param string $algorithm
     *
     * @return string
     */
    public function generateSaltedPassword($salt, $password, $algorithm = 'sha1')
    {
        return hash($algorithm, $salt . hash($algorithm, $password));
    }

    /**
     * Generiert einen Zufallsstring beliebiger Länge
     *
     * @param integer $length
     *
     * @return string
     */
    public function salt($length)
    {
        $salt = '';
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $cChars = strlen($chars) - 1;
        while (strlen($salt) < $length) {
            $char = $chars[mt_rand(0, $cChars)];
            // Zeichen nur hinzufügen, wenn sich dieses nicht bereits im Salz befindet
            if (strpos($salt, $char) === false) {
                $salt .= $char;
            }
        }
        return $salt;
    }

    /**
     * Enkodiert alle HTML-Entitäten eines Strings zur Vermeidung von XSS
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
}
