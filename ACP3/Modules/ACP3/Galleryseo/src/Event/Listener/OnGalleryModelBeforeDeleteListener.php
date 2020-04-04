<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Galleryseo\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Gallery\Helpers as GalleryHelpers;
use ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;

class OnGalleryModelBeforeDeleteListener
{
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    private $pictureRepository;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager
     */
    private $uriAliasManager;

    public function __construct(PictureRepository $pictureRepository, UriAliasManager $uriAliasManager)
    {
        $this->pictureRepository = $pictureRepository;
        $this->uriAliasManager = $uriAliasManager;
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
            $uri = \sprintf(GalleryHelpers::URL_KEY_PATTERN_GALLERY, $item);

            $this->uriAliasManager->deleteUriAlias($uri);

            $this->deletePictureAliases($item);
        }
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    private function deletePictureAliases(int $galleryId): void
    {
        foreach ($this->pictureRepository->getPicturesByGalleryId($galleryId) as $picture) {
            $uri = \sprintf(
                GalleryHelpers::URL_KEY_PATTERN_PICTURE,
                $picture['id']
            );

            $this->uriAliasManager->deleteUriAlias($uri);
        }
    }
}
