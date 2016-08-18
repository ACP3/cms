<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Event\Listener;


use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Articles\Cache;
use ACP3\Modules\ACP3\Articles\Helpers;
use ACP3\Modules\ACP3\Menus\Cache as MenusCache;
use ACP3\Modules\ACP3\Menus\Helpers\ManageMenuItem;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;

class OnArticlesModelDeleteAfterListener
{
    /**
     * @var Cache
     */
    protected $articlesCache;
    /**
     * @var MenusCache
     */
    protected $menusCache;
    /**
     * @var ManageMenuItem
     */
    protected $manageMenuItemHelper;
    /**
     * @var UriAliasManager
     */
    protected $uriAliasManager;

    /**
     * OnArticlesModelDeleteAfterListener constructor.
     * @param Cache $articlesCache
     */
    public function __construct(Cache $articlesCache)
    {
        $this->articlesCache = $articlesCache;
    }

    /**
     * @param MenusCache $menusCache
     * @return $this
     */
    public function setMenusCache(MenusCache $menusCache)
    {
        $this->menusCache = $menusCache;

        return $this;
    }

    /**
     * @param ManageMenuItem $manageMenuItemHelper
     * @return OnArticlesModelDeleteAfterListener
     */
    public function setManageMenuItemHelper(ManageMenuItem $manageMenuItemHelper)
    {
        $this->manageMenuItemHelper = $manageMenuItemHelper;

        return $this;
    }

    /**
     * @param UriAliasManager $uriAliasManager
     * @return OnArticlesModelDeleteAfterListener
     */
    public function setUriAliasManager(UriAliasManager $uriAliasManager)
    {
        $this->uriAliasManager = $uriAliasManager;

        return $this;
    }

    /**
     * @param ModelSaveEvent $event
     */
    public function execute(ModelSaveEvent $event)
    {
        if ($event->isDeleteStatement()) {
            return;
        }

        foreach ($event->getEntryId() as $entryId) {
            $this->articlesCache->getCacheDriver()->delete(Cache::CACHE_ID . $entryId);

            $uri = sprintf(Helpers::URL_KEY_PATTERN, $entryId);

            if ($this->manageMenuItemHelper) {
                $this->manageMenuItemHelper->manageMenuItem($uri, false);
            }

            if ($this->uriAliasManager) {
                $this->uriAliasManager->deleteUriAlias($uri);
            }
        }

        if ($this->menusCache) {
            $this->menusCache->saveMenusCache();
        }
    }
}
