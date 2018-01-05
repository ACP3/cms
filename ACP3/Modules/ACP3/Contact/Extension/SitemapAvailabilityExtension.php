<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Contact\Extension;

use ACP3\Modules\ACP3\Contact\Installer\Schema;
use ACP3\Modules\ACP3\Seo\Extension\AbstractSitemapAvailabilityExtension;

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
     * @inheritdoc
     */
    public function fetchSitemapUrls($isSecure = null)
    {
        $routeNames = [
            'contact/index/index',
            'contact/index/imprint',
        ];

        foreach ($routeNames as $routeName) {
            $this->addUrl($routeName, null, $isSecure);
        }
    }
}
