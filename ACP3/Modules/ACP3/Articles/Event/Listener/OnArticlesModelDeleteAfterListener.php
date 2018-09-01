<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Articles\Cache;
use ACP3\Modules\ACP3\Articles\Helpers;
use ACP3\Modules\ACP3\Menus\Helpers\ManageMenuItem;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;
use ACP3\Modules\ACP3\Share\Helpers\SocialSharingManager;

class OnArticlesModelDeleteAfterListener
{
    /**
     * @var Cache
     */
    protected $articlesCache;
    /**
     * @var ManageMenuItem
     */
    protected $manageMenuItemHelper;
    /**
     * @var UriAliasManager
     */
    protected $uriAliasManager;
    /**
     * @var \ACP3\Modules\ACP3\Share\Helpers\SocialSharingManager|null
     */
    private $socialSharingManager;

    /**
     * OnArticlesModelDeleteAfterListener constructor.
     *
     * @param Cache                                                      $articlesCache
     * @param \ACP3\Modules\ACP3\Menus\Helpers\ManageMenuItem|null       $manageMenuItemHelper
     * @param \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager|null         $uriAliasManager
     * @param \ACP3\Modules\ACP3\Share\Helpers\SocialSharingManager|null $socialSharingManager
     */
    public function __construct(
        Cache $articlesCache,
        ?ManageMenuItem $manageMenuItemHelper,
        ?UriAliasManager $uriAliasManager,
        ?SocialSharingManager $socialSharingManager
    ) {
        $this->articlesCache = $articlesCache;
        $this->manageMenuItemHelper = $manageMenuItemHelper;
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

        foreach ($event->getEntryId() as $entryId) {
            $this->articlesCache->getCacheDriver()->delete(Cache::CACHE_ID . $entryId);

            $uri = \sprintf(Helpers::URL_KEY_PATTERN, $entryId);

            if ($this->manageMenuItemHelper) {
                $this->manageMenuItemHelper->manageMenuItem($uri);
            }

            if ($this->uriAliasManager) {
                $this->uriAliasManager->deleteUriAlias($uri);
            }

            if ($this->socialSharingManager) {
                $this->socialSharingManager->deleteSharingInfo($uri);
            }
        }
    }
}
