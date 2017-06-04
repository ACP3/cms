<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Cache;

use ACP3\Core;
use ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository;

class FileCacheStorage extends Core\Cache\AbstractCacheStorage
{
    const CACHE_ID = 'details_id_';

    /**
     * @var \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository
     */
    protected $filesRepository;

    /**
     * @param \ACP3\Core\Cache\Cache                               $cache
     * @param \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository $filesRepository
     */
    public function __construct(
        Core\Cache\Cache $cache,
        FilesRepository $filesRepository
    ) {
        parent::__construct($cache);

        $this->filesRepository = $filesRepository;
    }

    /**
     * @param integer $fileId
     *
     * @return array
     */
    public function getCache($fileId)
    {
        if ($this->cache->contains(self::CACHE_ID . $fileId) === false) {
            $this->saveCache($fileId);
        }

        return $this->cache->fetch(self::CACHE_ID . $fileId);
    }

    /**
     * @param integer $fileId
     *
     * @return boolean
     */
    public function saveCache($fileId)
    {
        return $this->cache->save(self::CACHE_ID . $fileId, $this->filesRepository->getOneById($fileId));
    }
}
