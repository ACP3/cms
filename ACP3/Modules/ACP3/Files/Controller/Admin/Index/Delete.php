<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\Files;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Files\Controller\Admin\Index
 */
class Delete extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository
     */
    protected $filesRepository;
    /**
     * @var \ACP3\Modules\ACP3\Files\Cache
     */
    protected $filesCache;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Helpers
     */
    protected $commentsHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager
     */
    protected $uriAliasManager;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository $filesRepository
     * @param \ACP3\Modules\ACP3\Files\Cache $filesCache
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Files\Model\Repository\FilesRepository $filesRepository,
        Files\Cache $filesCache
    ) {
        parent::__construct($context);

        $this->filesRepository = $filesRepository;
        $this->filesCache = $filesCache;
    }

    /**
     * @param \ACP3\Modules\ACP3\Comments\Helpers $commentsHelpers
     *
     * @return $this
     */
    public function setCommentsHelpers(Comments\Helpers $commentsHelpers)
    {
        $this->commentsHelpers = $commentsHelpers;

        return $this;
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
            $action,
            function (array $items) {
                $bool = false;

                $upload = new Core\Helpers\Upload($this->appPath, 'files');
                foreach ($items as $item) {
                    if (!empty($item)) {
                        $upload->removeUploadedFile($this->filesRepository->getFileById($item)); // Datei ebenfalls löschen
                        $bool = $this->filesRepository->delete($item);
                        if ($this->commentsHelpers) {
                            $this->commentsHelpers->deleteCommentsByModuleAndResult(
                                $this->modules->getModuleId(Files\Installer\Schema::MODULE_NAME),
                                $item
                            );
                        }

                        $this->filesCache->getCacheDriver()->delete(Files\Cache::CACHE_ID);

                        if ($this->uriAliasManager) {
                            $this->uriAliasManager->deleteUriAlias(sprintf(Files\Helpers::URL_KEY_PATTERN, $item));
                        }
                    }
                }

                Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

                return $bool;
            }
        );
    }
}
