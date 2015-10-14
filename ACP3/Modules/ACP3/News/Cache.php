<?php
namespace ACP3\Modules\ACP3\News;

use ACP3\Core;
use ACP3\Modules\ACP3\News\Model\NewsRepository;

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
     * @param \ACP3\Modules\ACP3\News\Model\NewsRepository $newsRepository
     */
    public function __construct(
        Core\Cache $cache,
        NewsRepository $newsRepository
    )
    {
        parent::__construct($cache);

        $this->newsRepository = $newsRepository;
    }

    /**
     * Bindet die gecachete News ein
     *
     * @param integer $id
     *  Die ID der News
     *
     * @return array
     */
    public function getCache($id)
    {
        if ($this->cache->contains(self::CACHE_ID . $id) === false) {
            $this->saveCache($id);
        }

        return $this->cache->fetch(self::CACHE_ID . $id);
    }

    /**
     * Erstellt den Cache einer News anhand der angegebenen ID
     *
     * @param integer $id
     *  Die ID der News
     *
     * @return boolean
     */
    public function saveCache($id)
    {
        return $this->cache->save(self::CACHE_ID . $id, $this->newsRepository->getOneById($id));
    }
}
