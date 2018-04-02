<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Filesystem;

class Vendor
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var array
     */
    protected $vendors = [];

    /**
     * Vendors constructor.
     *
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     */
    public function __construct(ApplicationPath $appPath)
    {
        $this->appPath = $appPath;
    }

    /**
     * @return array
     */
    public function getVendors(): array
    {
        if ($this->vendors === []) {
            $this->cacheVendors();
        }

        return $this->vendors;
    }

    /**
     * Caches the various module vendors.
     */
    protected function cacheVendors(): void
    {
        $this->vendors = \array_merge(
            ['ACP3'],
            Filesystem::scandir($this->appPath->getModulesDir(), ['ACP3'])
        );
    }
}
