<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Galleryshare\EventListener;

use ACP3\Core\Model\Event\BeforeModelDeleteEvent;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Gallery\Helpers;
use ACP3\Modules\ACP3\Gallery\Repository\PictureRepository;
use ACP3\Modules\ACP3\Share\Helpers\SocialSharingManager;
use ACP3\Modules\ACP3\Share\Installer\Schema as ShareSchema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnGalleryModelBeforeDeleteListener implements EventSubscriberInterface
{
    public function __construct(private readonly Modules $modules, private readonly PictureRepository $pictureRepository, private readonly SocialSharingManager $socialSharingManager)
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(BeforeModelDeleteEvent $event): void
    {
        if (!$this->modules->isInstalled(ShareSchema::MODULE_NAME)) {
            return;
        }

        foreach ($event->getEntryId() as $item) {
            $uri = sprintf(Helpers::URL_KEY_PATTERN_GALLERY, $item);
            $this->socialSharingManager->deleteSharingInfo($uri);

            $this->deletePictureSocialSharing($item);
        }
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function deletePictureSocialSharing(int $galleryId): void
    {
        foreach ($this->pictureRepository->getPicturesByGalleryId($galleryId) as $picture) {
            $uri = sprintf(Helpers::URL_KEY_PATTERN_PICTURE, $picture['id']);

            $this->socialSharingManager->deleteSharingInfo($uri);
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'gallery.model.gallery.before_delete' => '__invoke',
        ];
    }
}
