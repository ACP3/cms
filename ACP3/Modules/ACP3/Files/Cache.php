<?php
namespace ACP3\Modules\ACP3\Files;

use ACP3\Core;

/**
 * Class Cache
 * @package ACP3\Modules\ACP3\Files
 */
class Cache extends Core\Modules\AbstractCacheStorage
{
    const CACHE_ID = 'details_id_';

    /**
     * @var \ACP3\Modules\ACP3\Files\Model
     */
    protected $filesModel;

    /**
     * @param Core\Cache $cache
     * @param Model $filesModel
     */
    public function __construct(
        Core\Cache $cache,
        Model $filesModel
    ) {
        parent::__construct($cache);

        $this->filesModel = $filesModel;
    }

    /**
     * @param integer $id
     *
     * @return array
     */
    public function getCache($id)
    {
        if ($this->cache->contains(self::CACHE_ID . $id) === false) {
            $this->setCache($id);
        }

        return $this->cache->fetch(self::CACHE_ID . $id);
    }

    /**
     * @param integer $id
     *
     * @return boolean
     */
    public function setCache($id)
    {
        return $this->cache->save(self::CACHE_ID . $id, $this->filesModel->getOneById($id));
    }
}
