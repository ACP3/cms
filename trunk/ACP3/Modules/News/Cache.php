<?php
namespace ACP3\Modules\News;

use ACP3\Core;

/**
 * Class Cache
 * @package ACP3\Modules\News
 */
class Cache
{
    const CACHE_ID = 'details_id_';
    /**
     * @var \ACP3\Core\Cache2
     */
    protected $cache;
    /**
     * @var Model
     */
    protected $newsModel;

    public function __construct(Model $newsModel)
    {
        $this->newsModel = $newsModel;
        $this->cache = new Core\Cache2('news');
    }

    /**
     * Erstellt den Cache einer News anhand der angegebenen ID
     *
     * @param integer $id
     *  Die ID der News
     * @return boolean
     */
    public function setCache($id)
    {
        return $this->cache->save(self::CACHE_ID . $id, $this->newsModel->getOneById($id));
    }

    /**
     * Bindet die gecachete News ein
     *
     * @param integer $id
     *  Die ID der News
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