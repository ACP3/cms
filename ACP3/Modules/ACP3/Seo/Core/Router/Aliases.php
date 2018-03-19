<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Core\Router;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo;

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
     * @var bool
     */
    private $isActive;

    /**
     * @param Core\Modules                 $modules
     * @param \ACP3\Modules\ACP3\Seo\Cache $seoCache
     */
    public function __construct(
        Core\Modules $modules,
        Seo\Cache $seoCache
    ) {
        $this->seoCache = $seoCache;
        $this->isActive = $modules->isActive(Seo\Installer\Schema::MODULE_NAME);
    }

    /**
     * Returns an uri alias by a given path.
     *
     * @param string $path
     * @param bool   $emptyOnNoResult
     *
     * @return string
     */
    public function getUriAlias(string $path, bool $emptyOnNoResult = false): string
    {
        if ($this->isActive === false) {
            return $path;
        }

        if ($this->aliasesCache === []) {
            $this->aliasesCache = $this->seoCache->getCache();
        }

        $path .= (!\preg_match('/\/$/', $path) ? '/' : '');

        return $this->aliasesCache[$path]['alias'] ?? ($emptyOnNoResult === true ? '' : $path);
    }

    /**
     * Checks, whether an uri alias exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public function uriAliasExists(string $path): bool
    {
        return $this->getUriAlias($path, true) !== '';
    }
}
