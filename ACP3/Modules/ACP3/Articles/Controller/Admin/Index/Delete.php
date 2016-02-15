<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\Menus;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Articles\Controller\Admin\Index
 */
class Delete extends AbstractFormAction
{
    /**
     * @var \ACP3\Modules\ACP3\Articles\Model\ArticleRepository
     */
    protected $articleRepository;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Cache
     */
    protected $articlesCache;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext          $context
     * @param \ACP3\Modules\ACP3\Articles\Model\ArticleRepository $articleRepository
     * @param \ACP3\Modules\ACP3\Articles\Cache                   $articlesCache
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Articles\Model\ArticleRepository $articleRepository,
        Articles\Cache $articlesCache)
    {
        parent::__construct($context);

        $this->articleRepository = $articleRepository;
        $this->articlesCache = $articlesCache;
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $this,
            $action,
            function ($items) {
                $bool = false;

                foreach ($items as $item) {
                    $uri = sprintf(Articles\Helpers::URL_KEY_PATTERN, $item);

                    $bool = $this->articleRepository->delete($item);

                    if ($this->manageMenuItemHelper) {
                        $this->manageMenuItemHelper->manageMenuItem($uri, false);
                    }

                    $this->articlesCache->getCacheDriver()->delete(Articles\Cache::CACHE_ID . $item);
                    $this->seo->deleteUriAlias($uri);
                }

                if ($this->menusCache) {
                    $this->menusCache->saveMenusCache();
                }

                return $bool;
            }
        );
    }
}
