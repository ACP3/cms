<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Utility;


use ACP3\Modules\ACP3\Seo\Extension\SitemapAvailabilityExtensionInterface;

class SitemapAvailabilityRegistrar
{
    /**
     * @var SitemapAvailabilityExtensionInterface[]
     */
    protected $availableModules = [];

    /**
     * @param SitemapAvailabilityExtensionInterface $availability
     */
    public function registerModule(SitemapAvailabilityExtensionInterface $availability)
    {
        $this->availableModules[$availability->getModuleName()] = $availability;
    }

    /**
     * @return SitemapAvailabilityExtensionInterface[]
     */
    public function getAvailableModules()
    {
        return $this->availableModules;
    }
}
