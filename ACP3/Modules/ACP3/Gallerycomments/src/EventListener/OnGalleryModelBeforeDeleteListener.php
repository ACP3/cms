<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallerycomments\EventListener;

use ACP3\Core\Model\Event\BeforeModelDeleteEvent;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Comments\Helpers as CommentsHelpers;
use ACP3\Modules\ACP3\Comments\Installer\Schema as CommentsSchema;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;
use ACP3\Modules\ACP3\Gallerycomments\Repository\GalleryPictureRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnGalleryModelBeforeDeleteListener implements EventSubscriberInterface
{
    public function __construct(private readonly Modules $modules, private readonly GalleryPictureRepository $galleryPictureRepository, private readonly CommentsHelpers $commentsHelpers)
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(BeforeModelDeleteEvent $event): void
    {
        if (!$this->modules->isInstalled(CommentsSchema::MODULE_NAME)) {
            return;
        }

        foreach ($event->getEntryIdList() as $galleryId) {
            $galleryPictures = $this->galleryPictureRepository->getPictureIdsByGalleryId($galleryId);

            foreach ($galleryPictures as $galleryPicture) {
                $this->commentsHelpers->deleteCommentsByModuleAndResult(
                    $this->modules->getModuleId(Schema::MODULE_NAME),
                    $galleryPicture
                );
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'gallery.model.gallery.before_delete' => '__invoke',
        ];
    }
}
