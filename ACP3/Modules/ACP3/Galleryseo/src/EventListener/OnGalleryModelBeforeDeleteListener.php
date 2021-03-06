<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Galleryseo\EventListener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Gallery\Helpers as GalleryHelpers;
use ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;
use ACP3\Modules\ACP3\Seo\Installer\Schema as SeoSchema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnGalleryModelBeforeDeleteListener implements EventSubscriberInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    private $pictureRepository;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager
     */
    private $uriAliasManager;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    public function __construct(Modules $modules, PictureRepository $pictureRepository, UriAliasManager $uriAliasManager)
    {
        $this->pictureRepository = $pictureRepository;
        $this->uriAliasManager = $uriAliasManager;
        $this->modules = $modules;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(ModelSaveEvent $event)
    {
        if (!$this->modules->isInstalled(SeoSchema::MODULE_NAME)) {
            return;
        }

        if (!$event->isDeleteStatement()) {
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
