<?php

namespace ACP3\Core;

/**
 * Class Functions
 * @package ACP3\Core
 */
class Functions
{
    /**
     * Enkodiert alle HTML-Entitäten eines Strings
     * zur Vermeidung von XSS
     *
     * @param string $var
     * @param boolean $scriptTagOnly
     *
     * @return string
     */
    public static function strEncode($var, $scriptTagOnly = false)
    {
        $var = preg_replace('=<script[^>]*>.*</script>=isU', '', $var);
        return $scriptTagOnly === true ? $var : htmlentities($var, ENT_QUOTES, 'UTF-8');
    }
}
