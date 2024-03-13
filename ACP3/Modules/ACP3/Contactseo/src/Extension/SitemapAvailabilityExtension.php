<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contactseo\Extension;

use ACP3\Modules\ACP3\Contact\Installer\Schema;
use ACP3\Modules\ACP3\Seo\Extension\AbstractSitemapAvailabilityExtension;

class SitemapAvailabilityExtension extends AbstractSitemapAvailabilityExtension
{
    public function getModuleName(): string
    {
        return Schema::MODULE_NAME;
    }

    public function fetchSitemapUrls(?bool $isSecure = null): void
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
