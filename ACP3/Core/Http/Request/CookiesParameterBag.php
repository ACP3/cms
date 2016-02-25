<?php
namespace ACP3\Core\Http\Request;

/**
 * Class CookieParameterBag
 * @package ACP3\Core\Http\Request
 */
class CookiesParameterBag extends ParameterBag
{
    /**
     * @param string $key
     * @param string $value
     * @param int    $expire
     * @param string $path
     * @param string $domain
     * @param bool   $isSecure
     * @param bool   $isHttpOnly
     */
    public function set($key, $value, $expire = 0, $path = '', $domain = '', $isSecure = false, $isHttpOnly = true)
    {
        setcookie($key, $value, $expire, $path, $domain, $isSecure, $isHttpOnly);

        parent::set($key, $value);
    }
}
