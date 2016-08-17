<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\News;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\News\Controller\Admin\Index
 */
class Delete extends Core\Controller\AbstractAdminAction
{
    use CommentsHelperTrait;

    /**
     * @var \ACP3\Modules\ACP3\News\Cache
     */
    protected $newsCache;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager
     */
    protected $uriAliasManager;
    /**
     * @var News\Model\NewsModel
     */
    protected $newsModel;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param News\Model\NewsModel $newsModel
     * @param \ACP3\Modules\ACP3\News\Cache $newsCache
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        News\Model\NewsModel $newsModel,
        News\Cache $newsCache)
    {
        parent::__construct($context);

        $this->newsCache = $newsCache;
        $this->newsModel = $newsModel;
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
            $action, function (array $items) {
            $bool = false;

            foreach ($items as $item) {
                $bool = $this->newsModel->delete($item);

                if ($this->commentsHelpers) {
                    $this->commentsHelpers->deleteCommentsByModuleAndResult(
                        $this->modules->getModuleId(News\Installer\Schema::MODULE_NAME),
                        $item
                    );
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
