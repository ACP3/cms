<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Cache;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo\Model\Repository\SeoRepository;

class SeoCacheStorage extends Core\Cache\AbstractCacheStorage
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Model\Repository\SeoRepository
     */
    private $seoRepository;

    /**
     * @param \ACP3\Core\Cache\Cache                                $cache
     * @param \ACP3\Modules\ACP3\Seo\Model\Repository\SeoRepository $seoRepository
     */
    public function __construct(
        Core\Cache\Cache $cache,
        SeoRepository $seoRepository
    ) {
        parent::__construct($cache);

        $this->seoRepository = $seoRepository;
    }

    /**
     * Gibt den Cache der URI-Aliase zurück.
     *
     * @return array
     */
    public function getCache()
    {
        if ($this->cache->contains('seo') === false) {
            $this->saveCache();
        }

        return $this->cache->fetch('seo');
    }

    /**
     * Setzt den Cache für die URI-Aliase.
     *
     * @return bool
     */
    public function saveCache()
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
