<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Utility;


class SitemapAvailabilityRegistrar
{
    /**
     * @var SitemapAvailabilityInterface[]
     */
    protected $availableModules = [];

    /**
     * @param SitemapAvailabilityInterface $availability
     */
    public function registerModule(SitemapAvailabilityInterface $availability)
    {
        $this->availableModules[$availability->getModuleName()] = $availability;
    }

    /**
     * @return SitemapAvailabilityInterface[]
     */
    public function getAvailableModules()
    {
        return $this->availableModules;
    }
}
