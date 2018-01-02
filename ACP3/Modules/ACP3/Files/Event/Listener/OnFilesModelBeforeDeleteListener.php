<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Event\Listener;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Helpers\Upload;
use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Modules\Modules;
use ACP3\Modules\ACP3\Comments\Helpers as CommentsHelpers;
use ACP3\Modules\ACP3\Files\Cache\FileCacheStorage;
use ACP3\Modules\ACP3\Files\Helpers;
use ACP3\Modules\ACP3\Files\Installer\Schema;
use ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;

class OnFilesModelBeforeDeleteListener
{
    /**
     * @var ApplicationPath
     */
    protected $applicationPath;
    /**
     * @var Modules
     */
    protected $modules;
    /**
     * @var FilesRepository
     */
    protected $filesRepository;
    /**
     * @var FileCacheStorage
     */
    protected $cache;
    /**
     * @var CommentsHelpers
     */
    protected $commentsHelpers;
    /**
     * @var UriAliasManager
     */
    protected $uriAliasManager;

    /**
     * OnFilesModelBeforeDeleteListener constructor.
     * @param ApplicationPath $applicationPath
     * @param Modules $modules
     * @param FilesRepository $filesRepository
     * @param FileCacheStorage $cache
     */
    public function __construct(
        ApplicationPath $applicationPath,
        Modules $modules,
        FilesRepository $filesRepository,
        FileCacheStorage $cache
    ) {
        $this->applicationPath = $applicationPath;
        $this->modules = $modules;
        $this->filesRepository = $filesRepository;
        $this->cache = $cache;
    }

    /**
     * @param CommentsHelpers $commentsHelpers
     *
     * @return $this
     */
    public function setCommentsHelpers(CommentsHelpers $commentsHelpers)
    {
        $this->commentsHelpers = $commentsHelpers;

        return $this;
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

        $upload = new Upload($this->applicationPath, Schema::MODULE_NAME);
        foreach ($event->getEntryId() as $item) {
            $upload->removeUploadedFile($this->filesRepository->getFileById($item));

            if ($this->commentsHelpers) {
                $this->commentsHelpers->deleteCommentsByModuleAndResult(
                    $this->modules->getModuleId(Schema::MODULE_NAME),
                    $item
                );
            }

            $this->cache->getCacheDriver()->delete(FileCacheStorage::CACHE_ID . $item);

            if ($this->uriAliasManager) {
                $this->uriAliasManager->deleteUriAlias(sprintf(Helpers::URL_KEY_PATTERN, $item));
            }
        }
    }
}
