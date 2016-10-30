<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Contact\Extension;


use ACP3\Modules\ACP3\Contact\Installer\Schema;
use ACP3\Modules\ACP3\Seo\Extension\AbstractSitemapAvailabilityExtension;
use Thepixeldeveloper\Sitemap\Url;

class SitemapAvailabilityExtension extends AbstractSitemapAvailabilityExtension
{
    /**
     * @return string
     */
    public function getModuleName()
    {
        return Schema::MODULE_NAME;
    }

    /**
     * @return Url[]
     */
    public function fetchSitemapItems()
    {
        $routeNames = [
            'contact/index/index',
            'contact/index/imprint'
        ];

        foreach ($routeNames as $routeName) {
            $this->addUrl($routeName);
        }

        return $this->getUrls();
    }
}
