<?php
namespace ACP3\Modules\Categories;

use ACP3\Core;

/**
 * Class Cache
 * @package ACP3\Modules\Categories
 */
class Cache
{
    /**
     * @var Model
     */
    protected $categoriesModel;
    /**
     * @var \ACP3\Core\Cache
     */
    protected $cache;

    /**
     * @param Core\Cache $cache
     * @param Model $categoriesModel
     */
    public function __construct(
        Core\Cache $cache,
        Model $categoriesModel
    ) {
        $this->categoriesModel = $categoriesModel;
        $this->cache = $cache;
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
