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
     * @param Aliases $aliases
     */
    function __construct(Aliases $aliases)
    {
        $this->aliases = $aliases;
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
        $prefix = ((bool)CONFIG_SEO_MOD_REWRITE === false || $isAdminUrl === true || (defined('DEBUG') && DEBUG === true)) ? PHP_SELF . '/' : ROOT_DIR;
        return $prefix . $path;
    }

}