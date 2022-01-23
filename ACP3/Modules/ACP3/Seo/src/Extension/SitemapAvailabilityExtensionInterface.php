<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Extension;

use Thepixeldeveloper\Sitemap\Url;

interface SitemapAvailabilityExtensionInterface
{
    public function getModuleName(): string;

    /**
     * @return Url[]
     */
    public function getUrls(?bool $isSecure = null): array;
}
