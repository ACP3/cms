<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\EventListener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator;
use ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PictureDeleteListener implements EventSubscriberInterface
{
    /**
     * @var ThumbnailGenerator
     */
    private $thumbnailGenerator;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    private $pictureRepository;

    /**
     * @var Array<int, array|null>
     */
    private $picturesToDelete = [];

    public function __construct(
        ThumbnailGenerator $thumbnailGenerator,
        PictureRepository $pictureRepository
    ) {
        $this->pictureRepository = $pictureRepository;
        $this->thumbnailGenerator = $thumbnailGenerator;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function beforeDelete(ModelSaveEvent $event): void
    {
        $entryIds = \is_array($event->getEntryId()) ? $event->getEntryId() : [$event->getEntryId()];

        foreach ($entryIds as $entryId) {
            $this->picturesToDelete[$entryId] = $this->pictureRepository->getOneById($entryId);
        }
    }

    /**
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     * @throws \Doctrine\DBAL\Exception
     */
    public function afterDelete(): void
    {
        if (!$this->picturesToDelete) {
            return;
        }

        foreach ($this->picturesToDelete as $pictureToDelete) {
            if (!$pictureToDelete) {
                continue;
            }

            $this->pictureRepository->updatePicturesNumbers($pictureToDelete['pic'], $pictureToDelete['gallery_id']);
            $this->thumbnailGenerator->removePictureFromFilesystem($pictureToDelete['file']);
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'gallery.model.gallery_pictures.before_delete' => 'beforeDelete',
            'gallery.model.gallery_pictures.after_delete' => 'afterDelete',
        ];
    }
}
