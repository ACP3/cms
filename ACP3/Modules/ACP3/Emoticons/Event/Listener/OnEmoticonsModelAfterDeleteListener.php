<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
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
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function __invoke(ModelSaveEvent $event)
    {
        if (!$event->isDeleteStatement()) {
            return;
        }

        $this->cache->saveCache();
    }
}
