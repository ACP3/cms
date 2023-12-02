<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\EventListener;

use ACP3\Core\Model\Event\BeforeModelDeleteEvent;
use ACP3\Modules\ACP3\Gallery\Model\PictureModel;
use ACP3\Modules\ACP3\Gallery\Repository\PictureRepository;
use Doctrine\DBAL\Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnGalleryModelBeforeDeleteListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly PictureRepository $pictureRepository,
        private readonly PictureModel $pictureModel)
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke(BeforeModelDeleteEvent $event): void
    {
        foreach ($event->getEntryIdList() as $item) {
            $this->deletePictureAliases($item);
        }
    }

    /**
     * @throws Exception
     */
    private function deletePictureAliases(int $galleryId): void
    {
        foreach ($this->pictureRepository->getPicturesByGalleryId($galleryId) as $picture) {
            $this->pictureModel->delete($picture['uri']);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'gallery.model.gallery.before_delete' => '__invoke',
        ];
    }
}
