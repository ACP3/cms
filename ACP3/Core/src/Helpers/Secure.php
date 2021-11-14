<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

class Secure
{
    /**
     * Generiert ein gesalzenes Passwort.
     */
    public function generateSaltedPassword(string $salt, string $password, string $algorithm = 'sha1'): string
    {
        return hash($algorithm, $salt . hash($algorithm, $password));
    }

    /**
     * Generiert einen Zufallsstring beliebiger Länge.
     *
     * @throws \Exception
     */
    public function salt(int $length): string
    {
        $salt = '';
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $cChars = \strlen($chars) - 1;
        while (\strlen($salt) < $length) {
            $char = $chars[random_int(0, $cChars)];
            if (!str_contains($salt, $char)) {
                $salt .= $char;
            }
        }

        return $salt;
    }

    /**
     * Enkodiert alle HTML-Entitäten eines Strings zur Vermeidung von XSS.
     */
    public function strEncode(string $var, bool $scriptTagOnly = false): string
    {
        $var = preg_replace('=<script[^>]*>.*</script>=isU', '', $var);

        return $scriptTagOnly === true ? $var : htmlspecialchars($var);
    }
}
