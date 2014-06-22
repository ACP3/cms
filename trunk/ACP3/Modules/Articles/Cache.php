<?php
namespace ACP3\Modules\Articles;

use ACP3\Core;

class Cache
{
    const CACHE_ID = 'list_id_';

    /**
     * @var Model
     */
    protected $articlesModel;
    /**
     * @var \ACP3\Core\Cache2
     */
    protected $cache;

    public function __construct(Model $articlesModel)
    {
        $this->articlesModel = $articlesModel;
        $this->cache = new Core\Cache2('articles');
    }

    /**
     * Erstellt den Cache eines Artikels anhand der angegebenen ID
     *
     * @param integer $id
     *  Die ID der statischen Seite
     * @return boolean
     */
    public function setCache($id)
    {
        return $this->cache->save(self::CACHE_ID . $id, $this->articlesModel->getOneById($id));
    }

    /**
     * Bindet den gecacheten Artikel ein
     *
     * @param integer $id
     *  Die ID der statischen Seite
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