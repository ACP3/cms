<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository;

/**
 * Class Cache
 * @package ACP3\Modules\ACP3\Categories
 */
class Cache extends Core\Modules\AbstractCacheStorage
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @param Core\Cache         $cache
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        Core\Cache $cache,
        CategoryRepository $categoryRepository
    ) {
        parent::__construct($cache);

        $this->categoryRepository = $categoryRepository;
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
            $this->saveCache($moduleName);
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
    public function saveCache($moduleName)
    {
        return $this->cache->save($moduleName, $this->categoryRepository->getAllByModuleName($moduleName));
    }
}
