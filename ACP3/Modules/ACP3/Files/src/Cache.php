<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files;

use ACP3\Core;
use ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository;

class Cache extends Core\Modules\AbstractCacheStorage
{
    public const CACHE_ID = 'details_id_';

    /**
     * @var \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository
     */
    protected $filesRepository;

    /**
     * @param \ACP3\Core\Cache $cache
     */
    public function __construct(
        Core\Cache $cache,
        FilesRepository $filesRepository
    ) {
        parent::__construct($cache);

        $this->filesRepository = $filesRepository;
    }

    /**
     * @param int $fileId
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
     * @param int $fileId
     *
     * @return bool
     */
    public function saveCache($fileId)
    {
        return $this->cache->save(self::CACHE_ID . $fileId, $this->filesRepository->getOneById($fileId));
    }
}
