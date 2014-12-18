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
    protected $aliases = [];

    /**
     * @param \ACP3\Modules\Seo\Cache $seoCache
     */
    public function __construct(Seo\Cache $seoCache)
    {
        $this->aliases = $seoCache->getCache();
    }

    /**
     * Gibt einen URI-Alias zurück
     *
     * @param string $path
     * @param bool   $emptyIsNoResult
     *
     * @return string
     */
    public function getUriAlias($path, $emptyIsNoResult = false)
    {
        $path .= !preg_match('/\/$/', $path) ? '/' : '';

        return !empty($this->aliases[$path]['alias']) ? $this->aliases[$path]['alias'] : ($emptyIsNoResult === true ? '' : $path);
    }

    /**
     * Überprüft, ob ein URI-Alias existiert
     *
     * @param string $path
     *
     * @return boolean
     */
    public function uriAliasExists($path)
    {
        $path .= !preg_match('/\/$/', $path) ? '/' : '';

        return isset($this->aliases[$path]) === true && !empty($this->aliases[$path]['alias']);
    }
}
