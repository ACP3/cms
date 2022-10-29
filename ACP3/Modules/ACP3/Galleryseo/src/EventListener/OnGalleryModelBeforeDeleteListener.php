<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Galleryseo\EventListener;

use ACP3\Core\Model\Event\BeforeModelDeleteEvent;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Gallery\Helpers as GalleryHelpers;
use ACP3\Modules\ACP3\Gallery\Repository\PictureRepository;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;
use ACP3\Modules\ACP3\Seo\Installer\Schema as SeoSchema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnGalleryModelBeforeDeleteListener implements EventSubscriberInterface
{
    public function __construct(private readonly Modules $modules, private readonly PictureRepository $pictureRepository, private readonly UriAliasManager $uriAliasManager)
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(BeforeModelDeleteEvent $event): void
    {
        if (!$this->modules->isInstalled(SeoSchema::MODULE_NAME)) {
            return;
        }

        foreach ($event->getEntryId() as $item) {
            $uri = sprintf(GalleryHelpers::URL_KEY_PATTERN_GALLERY, $item);

            $this->uriAliasManager->deleteUriAlias($uri);

            $this->deletePictureAliases($item);
        }
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function deletePictureAliases(int $galleryId): void
    {
        foreach ($this->pictureRepository->getPicturesByGalleryId($galleryId) as $picture) {
            $uri = sprintf(
                GalleryHelpers::URL_KEY_PATTERN_PICTURE,
                $picture['id']
            );

            $this->uriAliasManager->deleteUriAlias($uri);
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
