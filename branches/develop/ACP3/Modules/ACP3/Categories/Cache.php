<?php
namespace ACP3\Modules\ACP3\Categories;

use ACP3\Core;

/**
 * Class Cache
 * @package ACP3\Modules\ACP3\Categories
 */
class Cache extends Core\Modules\AbstractCacheStorage
{
    /**
     * @var Model
     */
    protected $categoriesModel;

    /**
     * @param Core\Cache $cache
     * @param Model $categoriesModel
     */
    public function __construct(
        Core\Cache $cache,
        Model $categoriesModel
    ) {
        parent::__construct($cache);

        $this->categoriesModel = $categoriesModel;
    }

    /**
     * Gibt die gecacheten Kategorien des jeweiligen Moduls zurück
     *
     * @param string $moduleName
     *  Das jeweilige Modul, für welches die Kategorien geholt werden sollen
     *
     * @return array
     */
    public function getCache($moduleName)
    {
        if ($this->cache->contains($moduleName) === false) {
            $this->setCache($moduleName);
        }

        return $this->cache->fetch($moduleName);
    }

    /**
     * Erstellt den Cache für die Kategorien eines Moduls
     *
     * @param string $moduleName
     *  Das Modul, für welches der Kategorien-Cache erstellt werden soll
     *
     * @return boolean
     */
    public function setCache($moduleName)
    {
        return $this->cache->save($moduleName, $this->categoriesModel->getAllByModuleName($moduleName));
    }
}
