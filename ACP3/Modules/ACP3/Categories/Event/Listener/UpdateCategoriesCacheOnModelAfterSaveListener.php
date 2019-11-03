<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Categories\Cache;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository;

class UpdateCategoriesCacheOnModelAfterSaveListener
{
    /**
     * @var Cache
     */
    protected $cache;
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * UpdateCategoriesCacheOnModelAfterSaveListener constructor.
     */
    public function __construct(Cache $cache, CategoryRepository $categoryRepository)
    {
        $this->cache = $cache;
        $this->categoryRepository = $categoryRepository;
    }

    public function __invoke(ModelSaveEvent $event)
    {
        $this->cache->saveCache($this->categoryRepository->getModuleNameFromCategoryId($event->getEntryId()));
    }
}
