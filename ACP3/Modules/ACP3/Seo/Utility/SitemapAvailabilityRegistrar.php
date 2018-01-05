<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Utility;

use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Seo\Extension\SitemapAvailabilityExtensionInterface;

class SitemapAvailabilityRegistrar
{
    /**
     * @var SitemapAvailabilityExtensionInterface[]
     */
    protected $availableModules = [];
    /**
     * @var Modules
     */
    private $modules;

    /**
     * SitemapAvailabilityRegistrar constructor.
     * @param Modules $modules
     */
    public function __construct(Modules $modules)
    {
        $this->modules = $modules;
    }

    /**
     * @param SitemapAvailabilityExtensionInterface $availability
     */
    public function registerModule(SitemapAvailabilityExtensionInterface $availability)
    {
        if ($this->modules->isActive($availability->getModuleName())) {
            $this->availableModules[$availability->getModuleName()] = $availability;
        }
    }

    /**
     * @return SitemapAvailabilityExtensionInterface[]
     */
    public function getAvailableModules()
    {
        return $this->availableModules;
    }
}
