<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Core;

use ACP3\Core;

/**
 * Class Router
 * @package ACP3\Installer\Core
 */
class Router extends Core\Router
{
    /**
     * @param \ACP3\Core\Http\RequestInterface       $request
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     */
    public function __construct(
        Core\Http\RequestInterface $request,
        Core\Environment\ApplicationPath $appPath
    ) {
        $this->request = $request;
        $this->appPath = $appPath;
    }

    /**
     * @param      $path
     * @param bool $absolute
     * @param bool $forceSecure
     *
     * @return string
     */
    public function route($path, $absolute = false, $forceSecure = false)
    {
        $path = $path . (!preg_match('/\/$/', $path) ? '/' : '');
        $pathArray = preg_split('=/=', $path, -1, PREG_SPLIT_NO_EMPTY);

        if (isset($pathArray[1]) === false) {
            $path .= 'index/';
        }
        if (isset($pathArray[2]) === false) {
            $path .= 'index/';
        }

        $prefix = '';
        // Append the current hostname to the URL
        if ($absolute === true) {
            $prefix .= ($forceSecure === true) ? 'https://' : $this->request->getProtocol();
            $prefix .= $this->request->getHostname();
        }

        $prefix .= $this->appPath->getPhpSelf() . '/';
        return $prefix . $path;
    }
}
