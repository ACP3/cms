<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Categories\Cache;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoriesRepository;

class UpdateCategoriesCacheOnModelAfterSaveListener
{
    /**
     * @var Cache
     */
    protected $cache;
    /**
     * @var CategoriesRepository
     */
    protected $categoryRepository;

    /**
     * UpdateCategoriesCacheOnModelAfterSaveListener constructor.
     * @param Cache $cache
     * @param CategoriesRepository $categoryRepository
     */
    public function __construct(Cache $cache, CategoriesRepository $categoryRepository)
    {
        $this->cache = $cache;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param ModelSaveEvent $event
     */
    public function execute(ModelSaveEvent $event)
    {
        $this->cache->saveCache($this->categoryRepository->getModuleNameFromCategoryId($event->getEntryId()));
    }
}
