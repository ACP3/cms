<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\EventListener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Categories\Cache;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UpdateCategoriesCacheOnModelAfterSaveListener implements EventSubscriberInterface
{
    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(Cache $cache, CategoryRepository $categoryRepository)
    {
        $this->cache = $cache;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(ModelSaveEvent $event): void
    {
        $this->cache->saveCache($this->categoryRepository->getModuleNameFromCategoryId($event->getEntryId()));
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'categories.model.categories.after_save' => '__invoke',
        ];
    }
}
