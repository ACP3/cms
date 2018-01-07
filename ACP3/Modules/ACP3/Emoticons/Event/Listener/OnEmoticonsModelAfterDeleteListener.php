<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Emoticons\Cache\EmoticonsCacheStorage;

class OnEmoticonsModelAfterDeleteListener
{
    /**
     * @var EmoticonsCacheStorage
     */
    protected $cache;

    /**
     * OnEmoticonsModelBeforeDeleteListener constructor.
     *
     * @param EmoticonsCacheStorage $cache
     */
    public function __construct(EmoticonsCacheStorage $cache)
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
