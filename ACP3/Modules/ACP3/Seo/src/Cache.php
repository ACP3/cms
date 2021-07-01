<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo\Model\Repository\SeoRepository;

class Cache extends Core\Modules\AbstractCacheStorage
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Model\Repository\SeoRepository
     */
    private $seoRepository;

    public function __construct(
        Core\Cache $cache,
        SeoRepository $seoRepository
    ) {
        parent::__construct($cache);

        $this->seoRepository = $seoRepository;
    }

    /**
     * Gibt den Cache der URI-Aliase zurück.
     */
    public function getCache(): array
    {
        if ($this->cache->contains('seo') === false) {
            $this->saveCache();
        }

        return $this->cache->fetch('seo');
    }

    /**
     * Setzt den Cache für die URI-Aliase.
     */
    public function saveCache(): bool
    {
        $data = [];
        foreach ($this->seoRepository->getAllMetaTags() as $alias) {
            $tmpAlias = $alias;
            unset($tmpAlias['uri']);

            $data[$alias['uri']] = $tmpAlias;
        }

        return $this->cache->save('seo', $data);
    }
}
