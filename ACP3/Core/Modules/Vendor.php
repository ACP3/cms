<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Modules;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Filesystem;

/**
 * Class Vendor
 * @package ACP3\Core\Modules
 */
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
    public function getVendors()
    {
        if ($this->vendors === []) {
            $this->cacheVendors();
        }

        return $this->vendors;
    }

    /**
     * Caches the various module vendors
     */
    protected function cacheVendors()
    {
        $this->vendors = array_merge(
            ['ACP3'],
            Filesystem::scandir($this->appPath->getModulesDir(), ['ACP3', 'Custom']),
            ['Custom']
        );
    }
}
