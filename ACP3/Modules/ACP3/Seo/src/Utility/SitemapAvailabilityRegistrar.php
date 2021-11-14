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
    private $availableModules = [];

    public function __construct(private Modules $modules)
    {
    }

    public function registerModule(SitemapAvailabilityExtensionInterface $availability): void
    {
        if ($this->modules->isInstalled($availability->getModuleName())) {
            $this->availableModules[$availability->getModuleName()] = $availability;
        }
    }

    /**
     * @return SitemapAvailabilityExtensionInterface[]
     */
    public function getAvailableModules(): array
    {
        return $this->availableModules;
    }
}
