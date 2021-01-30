<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\EventListener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Articles\Cache;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnArticlesModelDeleteAfterListener implements EventSubscriberInterface
{
    /**
     * @var Cache
     */
    private $articlesCache;

    public function __construct(Cache $articlesCache)
    {
        $this->articlesCache = $articlesCache;
    }

    public function __invoke(ModelSaveEvent $event)
    {
        if (!$event->isDeleteStatement()) {
            return;
        }

        foreach ($event->getEntryId() as $entryId) {
            $this->articlesCache->getCacheDriver()->delete(Cache::CACHE_ID . $entryId);
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'articles.model.articles.after_delete' => '__invoke',
        ];
    }
}
