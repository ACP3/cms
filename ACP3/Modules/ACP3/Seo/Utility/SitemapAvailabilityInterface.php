<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Utility;


use Thepixeldeveloper\Sitemap\Url;

interface SitemapAvailabilityInterface
{
    /**
     * @return string
     */
    public function getModuleName();

    /**
     * @return Url[]
     */
    public function fetchSitemapItems();
}
