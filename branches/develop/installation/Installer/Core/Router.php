<?php
namespace ACP3\Installer\Core;

use ACP3\Core;

/**
 * Class Router
 * @package ACP3\Installer\Core
 */
class Router extends Core\Router
{
    public function __construct()
    {
    }

    /**
     * Generiert die ACP3 internen Hyperlinks
     * @param $path
     * @return string
     */
    public function route($path)
    {
        $path = $path . (!preg_match('/\/$/', $path) ? '/' : '');
        $pathArray = preg_split('=/=', $path, -1, PREG_SPLIT_NO_EMPTY);

        if (isset($pathArray[1]) === false) {
            $path .= 'index/';
        }
        if (isset($pathArray[2]) === false) {
            $path .= 'index/';
        }

        $prefix = PHP_SELF . '/';
        return $prefix . $path;
    }

}