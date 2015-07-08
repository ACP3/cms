<?php
namespace ACP3\Modules\ACP3\Articles;

use ACP3\Core;

/**
 * Class Cache
 * @package ACP3\Modules\ACP3\Articles
 */
class Cache extends Core\Modules\AbstractCacheStorage
{
    const CACHE_ID = 'list_id_';

    /**
     * @var \ACP3\Modules\ACP3\Articles\Model
     */
    protected $articlesModel;

    /**
     * @param Core\Cache $cache
     * @param Model      $articlesModel
     */
    public function __construct(
        Core\Cache $cache,
        Model $articlesModel
    )
    {
        parent::__construct($cache);

        $this->articlesModel = $articlesModel;
    }

    /**
     * Bindet den gecacheten Artikel ein
     *
     * @param integer $id
     *  Die ID der statischen Seite
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
     * Erstellt den Cache eines Artikels anhand der angegebenen ID
     *
     * @param integer $id
     *  Die ID der statischen Seite
     *
     * @return boolean
     */
    public function setCache($id)
    {
        return $this->cache->save(self::CACHE_ID . $id, $this->articlesModel->getOneById($id));
    }
}
