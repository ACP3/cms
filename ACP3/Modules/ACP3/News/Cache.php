<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News;

use ACP3\Core;
use ACP3\Modules\ACP3\News\Model\Repository\NewsRepository;

class Cache extends Core\Modules\AbstractCacheStorage
{
    const CACHE_ID = 'details_id_';
    /**
     * @var NewsRepository
     */
    protected $newsRepository;

    /**
     * @param \ACP3\Core\Cache $cache
     */
    public function __construct(
        Core\Cache $cache,
        NewsRepository $newsRepository
    ) {
        parent::__construct($cache);

        $this->newsRepository = $newsRepository;
    }

    /**
     * Bindet die gecachete News ein.
     *
     * @param int $newsId
     *
     * @return array
     */
    public function getCache($newsId)
    {
        if ($this->cache->contains(self::CACHE_ID . $newsId) === false) {
            $this->saveCache($newsId);
        }

        return $this->cache->fetch(self::CACHE_ID . $newsId);
    }

    /**
     * Erstellt den Cache einer News anhand der angegebenen ID.
     *
     * @param int $newsId
     *
     * @return bool
     */
    public function saveCache($newsId)
    {
        return $this->cache->save(self::CACHE_ID . $newsId, $this->newsRepository->getOneById($newsId));
    }
}
