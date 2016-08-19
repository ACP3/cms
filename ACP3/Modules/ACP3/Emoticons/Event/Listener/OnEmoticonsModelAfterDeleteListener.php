<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Event\Listener;


use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Emoticons\Cache;

class OnEmoticonsModelAfterDeleteListener
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * OnEmoticonsModelBeforeDeleteListener constructor.
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param ModelSaveEvent $event
     */
    public function execute(ModelSaveEvent $event)
    {
        if (!$event->isDeleteStatement()) {
            return;
        }

        $this->cache->saveCache();
    }
}
