<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Core\Router;

use ACP3\Core;

class Router implements Core\Router\RouterInterface
{
    /**
     * @var Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var Core\Environment\ApplicationPath
     */
    protected $appPath;

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
     * @inheritdoc
     */
    public function route($path, $isAbsolute = false, $isSecure = null)
    {
        $path = $path . (!\preg_match('/\/$/', $path) ? '/' : '');
        $pathArray = \preg_split('=/=', $path, -1, PREG_SPLIT_NO_EMPTY);

        if (isset($pathArray[1]) === false) {
            $path .= 'index/';
        }
        if (isset($pathArray[2]) === false) {
            $path .= 'index/';
        }

        $prefix = '';
        // Append the current hostname to the URL
        if ($isAbsolute === true || $isSecure !== null) {
            $prefix .= $this->getScheme($isSecure);
            $prefix .= $this->request->getHost();
        }

        $prefix .= $this->appPath->getPhpSelf() . '/';

        return $prefix . $path;
    }

    /**
     * @param bool|null $isSecure
     * @return string
     */
    private function getScheme($isSecure)
    {
        if ($isSecure === null) {
            return $this->request->getScheme() . '://';
        } elseif ($isSecure === true) {
            return 'https://';
        }

        return 'http://';
    }
}
