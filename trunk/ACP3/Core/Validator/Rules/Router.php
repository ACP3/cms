<?php
namespace ACP3\Core\Validator\Rules;

/**
 * Class Router
 * @package ACP3\Core\Validator\Rules
 */
class Router
{
    /**
     * Überprüft, ob der eingegebene URI-Alias sicher ist, d.h. es dürfen nur
     * die Kleinbuchstaben von a-z, Zahlen, der Bindestrich und das Slash eingegeben werden
     *
     * @param string $var
     *
     * @return boolean
     */
    public function isUriSafe($var)
    {
        return (bool)preg_match('/^([a-z]{1}[a-z\d\-]*(\/[a-z\d\-]+)*)$/', $var);
    }

    /**
     * Überprüft, ob die übergebene URI dem Format des ACP3 entspricht
     *
     * @param mixed $var
     *
     * @return boolean
     */
    public function isInternalURI($var)
    {
        return (bool)preg_match('/^([a-z\d_\-]+\/){3,}$/', $var);
    }

} 