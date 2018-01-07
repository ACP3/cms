<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Cache;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles\Model\Repository\ArticlesRepository;

class ArticleCacheStorage extends Core\Cache\AbstractCacheStorage
{
    const CACHE_ID = 'list_id_';

    /**
     * @var \ACP3\Modules\ACP3\Articles\Model\Repository\ArticlesRepository
     */
    private $articleRepository;

    /**
     * @param \ACP3\Core\Cache\Cache $cache
     * @param ArticlesRepository     $articleRepository
     */
    public function __construct(
        Core\Cache\Cache $cache,
        ArticlesRepository $articleRepository
    ) {
        parent::__construct($cache);

        $this->articleRepository = $articleRepository;
    }

    /**
     * @param int $articleId
     *
     * @return array
     */
    public function getCache($articleId)
    {
        if ($this->cache->contains(self::CACHE_ID . $articleId) === false) {
            $this->saveCache($articleId);
        }

        return $this->cache->fetch(self::CACHE_ID . $articleId);
    }

    /**
     * @param int $articleId
     *
     * @return bool
     */
    public function saveCache($articleId)
    {
        return $this->cache->save(self::CACHE_ID . $articleId, $this->articleRepository->getOneById($articleId));
    }
}
