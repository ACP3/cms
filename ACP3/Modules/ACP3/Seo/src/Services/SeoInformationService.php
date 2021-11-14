<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Services;

use ACP3\Modules\ACP3\Seo\Repository\SeoRepository;

class SeoInformationService
{
    /**
     * @var array<string, array<string, mixed>>|null
     */
    private $cache;

    public function __construct(private SeoRepository $seoRepository)
    {
    }

    /**
     * Returns all the available meta information regarding a route.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getAllSeoInformation(): array
    {
        if ($this->cache === null) {
            $this->cache = [];
            foreach ($this->seoRepository->getAllMetaTags() as $alias) {
                $tmpAlias = $alias;
                unset($tmpAlias['uri']);

                $this->cache[$alias['uri']] = $tmpAlias;
            }
        }

        return $this->cache;
    }
}
