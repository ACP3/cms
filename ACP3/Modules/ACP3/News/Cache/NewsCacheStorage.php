<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Cache;

use ACP3\Core;
use ACP3\Modules\ACP3\News\Model\Repository\NewsRepository;

class NewsCacheStorage extends Core\Cache\AbstractCacheStorage
{
    const CACHE_ID = 'details_id_';
    /**
     * @var NewsRepository
     */
    private $newsRepository;

    /**
     * @param \ACP3\Core\Cache\Cache                             $cache
     * @param \ACP3\Modules\ACP3\News\Model\Repository\NewsRepository $newsRepository
     */
    public function __construct(
        Core\Cache\Cache $cache,
        NewsRepository $newsRepository
    ) {
        parent::__construct($cache);

        $this->newsRepository = $newsRepository;
    }

    /**
     * Bindet die gecachete News ein
     *
     * @param integer $newsId
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
     * Erstellt den Cache einer News anhand der angegebenen ID
     *
     * @param integer $newsId
     *
     * @return boolean
     */
    public function saveCache($newsId)
    {
        return $this->cache->save(self::CACHE_ID . $newsId, $this->newsRepository->getOneById($newsId));
    }
}
