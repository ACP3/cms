<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Articles\Controller\Admin\Index
 */
class Delete extends AbstractFormAction
{
    /**
     * @var \ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository
     */
    protected $articleRepository;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Cache
     */
    protected $articlesCache;

    /**
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param Core\Helpers\Forms $formsHelper
     * @param \ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository $articleRepository
     * @param \ACP3\Modules\ACP3\Articles\Cache $articlesCache
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\Forms $formsHelper,
        Articles\Model\Repository\ArticleRepository $articleRepository,
        Articles\Cache $articlesCache)
    {
        parent::__construct($context, $formsHelper);

        $this->articleRepository = $articleRepository;
        $this->articlesCache = $articlesCache;
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $action, function ($items) {
            $bool = false;

            foreach ($items as $item) {
                $uri = sprintf(Articles\Helpers::URL_KEY_PATTERN, $item);

                $bool = $this->articleRepository->delete($item);

                if ($this->manageMenuItemHelper) {
                    $this->manageMenuItemHelper->manageMenuItem($uri, false);
                }

                $this->articlesCache->getCacheDriver()->delete(Articles\Cache::CACHE_ID . $item);

                if ($this->uriAliasManager) {
                    $this->uriAliasManager->deleteUriAlias($uri);
                }
            }

            if ($this->menusCache) {
                $this->menusCache->saveMenusCache();
            }

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $bool;
        }
        );
    }
}
