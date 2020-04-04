<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Event\Listener;

use ACP3\Core\Helpers\Upload;
use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Comments\Helpers as CommentsHelpers;
use ACP3\Modules\ACP3\Files\Cache;
use ACP3\Modules\ACP3\Files\Helpers;
use ACP3\Modules\ACP3\Files\Installer\Schema;
use ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository;
use ACP3\Modules\ACP3\Share\Helpers\SocialSharingManager;

class OnFilesModelBeforeDeleteListener
{
    /**
     * @var Modules
     */
    private $modules;
    /**
     * @var FilesRepository
     */
    private $filesRepository;
    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var CommentsHelpers
     */
    private $commentsHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Share\Helpers\SocialSharingManager|null
     */
    private $socialSharingManager;
    /**
     * @var \ACP3\Core\Helpers\Upload
     */
    private $filesUploadHelper;

    public function __construct(
        Modules $modules,
        Upload $filesUploadHelper,
        FilesRepository $filesRepository,
        Cache $cache,
        ?CommentsHelpers $commentsHelpers,
        ?SocialSharingManager $socialSharingManager
    ) {
        $this->modules = $modules;
        $this->filesRepository = $filesRepository;
        $this->cache = $cache;
        $this->commentsHelpers = $commentsHelpers;
        $this->socialSharingManager = $socialSharingManager;
        $this->filesUploadHelper = $filesUploadHelper;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(ModelSaveEvent $event)
    {
        if (!$event->isDeleteStatement()) {
            return;
        }

        foreach ($event->getEntryId() as $item) {
            $this->filesUploadHelper->removeUploadedFile($this->filesRepository->getFileById($item));

            if ($this->commentsHelpers) {
                $this->commentsHelpers->deleteCommentsByModuleAndResult(
                    $this->modules->getModuleId(Schema::MODULE_NAME),
                    $item
                );
            }

            $this->cache->getCacheDriver()->delete(Cache::CACHE_ID . $item);

            $uri = \sprintf(Helpers::URL_KEY_PATTERN, $item);

            if ($this->socialSharingManager) {
                $this->socialSharingManager->deleteSharingInfo($uri);
            }
        }
    }
}
