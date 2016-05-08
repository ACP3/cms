<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\News;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\News\Controller\Admin\Index
 */
class Delete extends Core\Controller\AdminAction
{
    use CommentsHelperTrait;

    /**
     * @var \ACP3\Modules\ACP3\News\Model\NewsRepository
     */
    protected $newsRepository;
    /**
     * @var \ACP3\Modules\ACP3\News\Cache
     */
    protected $newsCache;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager
     */
    protected $uriAliasManager;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext   $context
     * @param \ACP3\Modules\ACP3\News\Model\NewsRepository $newsRepository
     * @param \ACP3\Modules\ACP3\News\Cache                $newsCache
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        News\Model\NewsRepository $newsRepository,
        News\Cache $newsCache)
    {
        parent::__construct($context);

        $this->newsRepository = $newsRepository;
        $this->newsCache = $newsCache;
    }

    /**
     * @param \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager $uriAliasManager
     */
    public function setUriAliasManager(UriAliasManager $uriAliasManager)
    {
        $this->uriAliasManager = $uriAliasManager;
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
            $this,
            $action,
            function ($items) {
                $bool = false;

                foreach ($items as $item) {
                    $bool = $this->newsRepository->delete($item);
                    if ($this->commentsHelpers) {
                        $this->commentsHelpers->deleteCommentsByModuleAndResult('news', $item);
                    }

                    $this->newsCache->getCacheDriver()->delete(News\Cache::CACHE_ID . $item);

                    if ($this->uriAliasManager) {
                        $this->uriAliasManager->deleteUriAlias(sprintf(News\Helpers::URL_KEY_PATTERN, $item));
                    }
                }

                return $bool;
            }
        );
    }
}
