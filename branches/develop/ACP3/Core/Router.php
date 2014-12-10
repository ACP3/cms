<?php

namespace ACP3\Core;

use ACP3\Core\Router\Aliases;
use ACP3\Modules\System;

/**
 * Class Router
 * @package ACP3\Core
 */
class Router
{
    const ADMIN_PANEL_PATTERN = '=^acp/=';

    /**
     * @var Aliases
     */
    protected $aliases;
    /**
     * @var array
     */
    protected $seoConfig = [];

    /**
     * @var string
     */
    private $protocol = '';
    /**
     * @var string
     */
    private $hostname = '';

    /**
     * @param Aliases $aliases
     * @param Config $seoConfig
     */
    public function __construct(
        Aliases $aliases,
        Config $seoConfig
    ) {
        $this->aliases = $aliases;
        $this->seoConfig = $seoConfig->getSettings();

        $this->_setBaseUrl();
    }

    /**
     * Sets the base url (Protocol + Hostname)
     */
    protected function _setBaseUrl()
    {
        $this->protocol = empty($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) === 'off' ? 'http://' : 'https://';
        $this->hostname = htmlentities($_SERVER['HTTP_HOST'], ENT_QUOTES, 'UTF-8');
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Generiert die ACP3 internen Hyperlinks
     *
     * @param $path
     * @param bool $absolute
     * @param bool $forceSecure
     * @return string
     */
    public function route($path, $absolute = false, $forceSecure = false)
    {
        $isAdminUrl = false;

        if ($path !== '') {
            $path = $path . (!preg_match('/\/$/', $path) ? '/' : '');
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
            $prefix .= ($forceSecure === true) ? 'https://' : $this->protocol;
            $prefix .= $this->hostname;
        }

        // Check, whether to use urls with mod_rewrite or not
        if ((bool)$this->seoConfig['seo_mod_rewrite'] === false ||
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
