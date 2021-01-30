<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\EventListener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Articles\Cache;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UpdateArticleCacheOnModelSaveAfterListener implements EventSubscriberInterface
{
    /**
     * @var Cache
     */
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function __invoke(ModelSaveEvent $event)
    {
        $this->cache->saveCache($event->getEntryId());
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'articles.model.articles.after_save' => '__invoke',
        ];
    }
}
