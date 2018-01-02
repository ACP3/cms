<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Modules\Modules;
use ACP3\Modules\ACP3\News\Cache\NewsCacheStorage;
use ACP3\Modules\ACP3\News\Controller\Admin\Index\CommentsHelperTrait;
use ACP3\Modules\ACP3\News\Helpers;
use ACP3\Modules\ACP3\News\Installer\Schema;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;

class OnNewsModelAfterDeleteListener
{
    use CommentsHelperTrait;

    /**
     * @var Modules
     */
    protected $modules;
    /**
     * @var UriAliasManager
     */
    protected $uriAliasManager;
    /**
     * @var NewsCacheStorage
     */
    protected $cache;

    /**
     * OnNewsModelAfterDeleteListener constructor.
     * @param Modules $modules
     * @param NewsCacheStorage $cache
     */
    public function __construct(
        Modules $modules,
        NewsCacheStorage $cache
    ) {
        $this->modules = $modules;
        $this->cache = $cache;
    }

    /**
     * @param UriAliasManager $uriAliasManager
     */
    public function setUriAliasManager(UriAliasManager $uriAliasManager)
    {
        $this->uriAliasManager = $uriAliasManager;
    }

    /**
     * @param ModelSaveEvent $event
     */
    public function execute(ModelSaveEvent $event)
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

            $this->cache->getCacheDriver()->delete(NewsCacheStorage::CACHE_ID . $item);

            if ($this->uriAliasManager) {
                $this->uriAliasManager->deleteUriAlias(sprintf(Helpers::URL_KEY_PATTERN, $item));
            }
        }
    }
}
