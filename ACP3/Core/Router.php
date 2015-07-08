<?php

namespace ACP3\Core;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Router\Aliases;
use ACP3\Modules\ACP3\System;

/**
 * Class Router
 * @package ACP3\Core
 */
class Router
{
    const ADMIN_PANEL_PATTERN = '=^acp/=';

    /**
     * @var \ACP3\Core\Router\Aliases
     */
    protected $aliases;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;

    /**
     * @param \ACP3\Core\Router\Aliases        $aliases
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\Config                $config
     */
    public function __construct(
        Aliases $aliases,
        RequestInterface $request,
        Config $config
    )
    {
        $this->aliases = $aliases;
        $this->request = $request;
        $this->config = $config;
    }

    /**
     * Generates the internal ACP3 hyperlinks
     *
     * @param      $path
     * @param bool $absolute
     * @param bool $forceSecure
     *
     * @return string
     */
    public function route($path, $absolute = false, $forceSecure = false)
    {
        $isAdminUrl = false;

        if ($path !== '') {
            $path = $path . (!preg_match('/\/$/', $path) ? '/' : '');
            if ($path === 'acp/') {
                $path = 'acp/acp/index/index/';
            }
            $pathArray = preg_split('=/=', $path, -1, PREG_SPLIT_NO_EMPTY);
            $isAdminUrl = preg_match(self::ADMIN_PANEL_PATTERN, $path) === true;

            if ($isAdminUrl === true) {
                if (isset($pathArray[2]) === false) {
                    $path .= 'index/';
                }
                if (isset($pathArray[3]) === false) {
                    $path .= 'index/';
                }
            } else {
                if (isset($pathArray[1]) === false) {
                    $path .= 'index/';
                }
                if (isset($pathArray[2]) === false) {
                    $path .= 'index/';
                }
            }

            if ($isAdminUrl === false) {
                $alias = $this->aliases->getUriAlias($path);
                $path = $alias . (!preg_match('/\/$/', $alias) ? '/' : '');
            }
        }

        $prefix = '';
        // Append the current hostname to the URL
        if ($absolute === true) {
            $prefix .= ($forceSecure === true) ? 'https://' : $this->request->getProtocol();
            $prefix .= $this->request->getHostname();
        }

        // Check, whether to use urls with mod_rewrite or not
        if ((bool)$this->config->getSettings('seo')['mod_rewrite'] === false ||
            $isAdminUrl === true ||
            (defined('DEBUG') && DEBUG === true)
        ) {
            $prefix .= PHP_SELF . '/';
        } else {
            $prefix .= ROOT_DIR;
        }

        return $prefix . $path;
    }
}
