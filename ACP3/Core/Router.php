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
    protected $systemConfig = [];

    /**
     * @param Aliases $aliases
     * @param Config $systemConfig
     */
    function __construct(
        Aliases $aliases,
        Config $systemConfig
    )
    {
        $this->aliases = $aliases;
        $this->systemConfig = $systemConfig->getSettings();
    }

    /**
     * Generiert die ACP3 internen Hyperlinks
     *
     * @param $path
     *
     * @return string
     */
    public function route($path)
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

        $prefix = ((bool)$this->systemConfig['seo_mod_rewrite'] === false || $isAdminUrl === true || (defined('DEBUG') && DEBUG === true)) ? PHP_SELF . '/' : ROOT_DIR;
        return $prefix . $path;
    }

}