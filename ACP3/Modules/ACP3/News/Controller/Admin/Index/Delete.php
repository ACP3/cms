<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\News;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\News\Controller\Admin\Index
 */
class Delete extends Core\Modules\AdminController
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
     * Delete constructor.
     *
     * @param \ACP3\Core\Modules\Controller\AdminContext   $context
     * @param \ACP3\Modules\ACP3\News\Model\NewsRepository $newsRepository
     * @param \ACP3\Modules\ACP3\News\Cache                $newsCache
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        News\Model\NewsRepository $newsRepository,
        News\Cache $newsCache)
    {
        parent::__construct($context);

        $this->newsRepository = $newsRepository;
        $this->newsCache = $newsCache;
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
                    $bool = $this->newsRepository->delete($item);
                    if ($this->commentsHelpers) {
                        $this->commentsHelpers->deleteCommentsByModuleAndResult('news', $item);
                    }

                    $this->newsCache->getCacheDriver()->delete(News\Cache::CACHE_ID . $item);
                    $this->seo->deleteUriAlias(sprintf(News\Helpers::URL_KEY_PATTERN, $item));
                }

                return $bool;
            }
        );
    }
}
