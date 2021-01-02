<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Gallery\Cache;
use ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UpdatePictureCacheOnModelAfterSaveListener implements EventSubscriberInterface
{
    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    private $pictureRepository;

    public function __construct(Cache $cache, PictureRepository $pictureRepository)
    {
        $this->cache = $cache;
        $this->pictureRepository = $pictureRepository;
    }

    /**
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(ModelSaveEvent $event): void
    {
        if ($event->isDeleteStatement()) {
            return;
        }

        $galleryId = $this->pictureRepository->getGalleryIdFromPictureId($event->getEntryId());

        $this->cache->saveCache($galleryId);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'gallery.model.gallery_pictures.after_save' => '__invoke',
        ];
    }
}
