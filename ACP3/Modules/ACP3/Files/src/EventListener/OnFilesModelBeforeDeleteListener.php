<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\EventListener;

use ACP3\Core\Helpers\Upload;
use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Files\Cache;
use ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnFilesModelBeforeDeleteListener implements EventSubscriberInterface
{
    /**
     * @var FilesRepository
     */
    private $filesRepository;
    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var \ACP3\Core\Helpers\Upload
     */
    private $filesUploadHelper;

    public function __construct(
        Upload $filesUploadHelper,
        FilesRepository $filesRepository,
        Cache $cache
    ) {
        $this->filesRepository = $filesRepository;
        $this->cache = $cache;
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

            $this->cache->getCacheDriver()->delete(Cache::CACHE_ID . $item);
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'files.model.files.before_delete' => '__invoke',
        ];
    }
}
