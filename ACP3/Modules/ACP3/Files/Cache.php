<?php
namespace ACP3\Modules\ACP3\Files;

use ACP3\Core;
use ACP3\Modules\ACP3\Files\Model\FilesRepository;

/**
 * Class Cache
 * @package ACP3\Modules\ACP3\Files
 */
class Cache extends Core\Modules\AbstractCacheStorage
{
    const CACHE_ID = 'details_id_';

    /**
     * @var \ACP3\Modules\ACP3\Files\Model\FilesRepository
     */
    protected $filesRepository;

    /**
     * @param \ACP3\Core\Cache                               $cache
     * @param \ACP3\Modules\ACP3\Files\Model\FilesRepository $filesRepository
     */
    public function __construct(
        Core\Cache $cache,
        FilesRepository $filesRepository
    )
    {
        parent::__construct($cache);

        $this->filesRepository = $filesRepository;
    }

    /**
     * @param integer $id
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
     * @param integer $id
     *
     * @return boolean
     */
    public function saveCache($id)
    {
        return $this->cache->save(self::CACHE_ID . $id, $this->filesRepository->getOneById($id));
    }
}
