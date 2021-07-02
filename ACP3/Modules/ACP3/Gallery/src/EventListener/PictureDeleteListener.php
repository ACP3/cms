<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\EventListener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Gallery\Helpers;
use ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PictureDeleteListener implements EventSubscriberInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Helpers
     */
    private $galleryHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    private $pictureRepository;

    /**
     * @var Array<int, array|null>
     */
    private $picturesToDelete = [];

    public function __construct(
        Helpers $galleryHelpers,
        PictureRepository $pictureRepository
    ) {
        $this->galleryHelpers = $galleryHelpers;
        $this->pictureRepository = $pictureRepository;
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
            $this->galleryHelpers->removePicture($pictureToDelete['file']);
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
