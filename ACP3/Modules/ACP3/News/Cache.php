<?php
namespace ACP3\Modules\ACP3\News;

use ACP3\Core;
use ACP3\Modules\ACP3\News\Model\Repository\NewsRepository;

/**
 * Class Cache
 * @package ACP3\Modules\ACP3\News
 */
class Cache extends Core\Modules\AbstractCacheStorage
{
    const CACHE_ID = 'details_id_';
    /**
     * @var NewsRepository
     */
    protected $newsRepository;

    /**
     * @param \ACP3\Core\Cache                             $cache
     * @param \ACP3\Modules\ACP3\News\Model\Repository\NewsRepository $newsRepository
     */
    public function __construct(
        Core\Cache $cache,
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
