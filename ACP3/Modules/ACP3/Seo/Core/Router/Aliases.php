<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Core\Router;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo;

/**
 * Class Aliases
 * @package ACP3\Modules\ACP3\Seo\Core\Router
 */
class Aliases
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Cache
     */
    protected $seoCache;
    /**
     * @var array
     */
    protected $aliasesCache = [];

    /**
     * @param \ACP3\Modules\ACP3\Seo\Cache $seoCache
     */
    public function __construct(Seo\Cache $seoCache)
    {
        $this->seoCache = $seoCache;
    }

    /**
     * Returns an uri alias by a given path
     *
     * @param string $path
     * @param bool   $emptyOnNoResult
     *
     * @return string
     */
    public function getUriAlias($path, $emptyOnNoResult = false)
    {
        if ($this->aliasesCache === []) {
            $this->aliasesCache = $this->seoCache->getCache();
        }

        $path .= !preg_match('/\/$/', $path) ? '/' : '';

        return !empty($this->aliasesCache[$path]['alias'])
            ? $this->aliasesCache[$path]['alias']
            : ($emptyOnNoResult === true ? '' : $path);
    }

    /**
     * Checks, whether an uri alias exists
     *
     * @param string $path
     *
     * @return boolean
     */
    public function uriAliasExists($path)
    {
        return ($this->getUriAlias($path, true) !== '');
    }
}
