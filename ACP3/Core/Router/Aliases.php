<?php
namespace ACP3\Core\Router;

use ACP3\Core;
use ACP3\Modules\Seo;

/**
 * Class Aliases
 * @package ACP3\Core\Router
 */
class Aliases
{
    /**
     * @var array
     */
    protected $aliasesCache = [];

    /**
     * @param \ACP3\Modules\Seo\Cache $seoCache
     */
    public function __construct(Seo\Cache $seoCache)
    {
        $this->aliasesCache = $seoCache->getCache();
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
        $path .= !preg_match('/\/$/', $path) ? '/' : '';

        return !empty($this->aliasesCache[$path]['alias']) ? $this->aliasesCache[$path]['alias'] : ($emptyOnNoResult === true ? '' : $path);
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
