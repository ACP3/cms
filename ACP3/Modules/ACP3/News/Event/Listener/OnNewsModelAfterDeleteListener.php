<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\News\Cache;
use ACP3\Modules\ACP3\News\Helpers;
use ACP3\Modules\ACP3\News\Installer\Schema;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;
use ACP3\Modules\ACP3\Share\Helpers\SocialSharingManager;

class OnNewsModelAfterDeleteListener
{
    /**
     * @var Modules
     */
    protected $modules;
    /**
     * @var UriAliasManager
     */
    protected $uriAliasManager;
    /**
     * @var Cache
     */
    protected $cache;
    /**
     * @var \ACP3\Modules\ACP3\Share\Helpers\SocialSharingManager|null
     */
    private $socialSharingManager;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Helpers|null
     */
    private $commentsHelpers;

    /**
     * OnNewsModelAfterDeleteListener constructor.
     *
     * @param Modules                                                    $modules
     * @param Cache                                                      $cache
     * @param \ACP3\Modules\ACP3\Comments\Helpers|null                   $commentsHelpers
     * @param \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager|null         $uriAliasManager
     * @param \ACP3\Modules\ACP3\Share\Helpers\SocialSharingManager|null $socialSharingManager
     */
    public function __construct(
        Modules $modules,
        Cache $cache,
        ?\ACP3\Modules\ACP3\Comments\Helpers $commentsHelpers,
        ?UriAliasManager $uriAliasManager,
        ?SocialSharingManager $socialSharingManager
    ) {
        $this->modules = $modules;
        $this->cache = $cache;
        $this->commentsHelpers = $commentsHelpers;
        $this->uriAliasManager = $uriAliasManager;
        $this->socialSharingManager = $socialSharingManager;
    }

    /**
     * @param ModelSaveEvent $event
     *
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

            $uri = \sprintf(Helpers::URL_KEY_PATTERN, $item);
            if ($this->uriAliasManager) {
                $this->uriAliasManager->deleteUriAlias($uri);
            }
            if ($this->socialSharingManager) {
                $this->socialSharingManager->deleteSharingInfo($uri);
            }
        }
    }
}
