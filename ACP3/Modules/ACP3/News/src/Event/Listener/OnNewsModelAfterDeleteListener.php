<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\News\Cache;
use ACP3\Modules\ACP3\News\Installer\Schema;

class OnNewsModelAfterDeleteListener
{
    /**
     * @var Modules
     */
    private $modules;
    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Helpers|null
     */
    private $commentsHelpers;

    public function __construct(
        Modules $modules,
        Cache $cache,
        ?\ACP3\Modules\ACP3\Comments\Helpers $commentsHelpers
    ) {
        $this->modules = $modules;
        $this->cache = $cache;
        $this->commentsHelpers = $commentsHelpers;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(ModelSaveEvent $event)
    {
        if (!$event->isDeleteStatement()) {
            return;
        }

        foreach ($event->getEntryId() as $item) {
            if ($this->commentsHelpers) {
                $this->commentsHelpers->deleteCommentsByModuleAndResult(
                    $this->modules->getModuleId(Schema::MODULE_NAME),
                    $item
                );
            }

            $this->cache->getCacheDriver()->delete(Cache::CACHE_ID . $item);
        }
    }
}
