<?php
namespace ACP3\Modules\Files;

use ACP3\Core;

/**
 * Class Cache
 * @package ACP3\Modules\Files
 */
class Cache
{
    const CACHE_ID = 'details_id_';

    /**
     * @var Core\Cache
     */
    protected $cache;
    /**
     * @var Model
     */
    protected $filesModel;

    public function __construct(Model $filesModel)
    {
        $this->filesModel = $filesModel;
        $this->cache = new Core\Cache('files');
    }

    /**
     * @param integer $id
     * @return boolean
     */
    public function setCache($id)
    {
        return $this->cache->save(self::CACHE_ID . $id, $this->filesModel->getOneById($id));
    }

    /**
     * @param integer $id
     * @return array
     */
    public function getCache($id)
    {
        if ($this->cache->contains(self::CACHE_ID . $id) === false) {
            $this->setCache($id);
        }

        return $this->cache->fetch(self::CACHE_ID . $id);
    }

} 