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

}
