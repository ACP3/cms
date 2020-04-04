<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Galleryseo\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Gallery;
use ACP3\Modules\ACP3\Seo\Core\Router\Aliases;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;

class GeneratePictureUriAliasesOnModelAfterSaveListener
{
    /**
     * @var Gallery\Model\Repository\PictureRepository
     */
    private $pictureRepository;
    /**
     * @var Aliases
     */
    private $aliases;
    /**
     * @var UriAliasManager
     */
    private $uriAliasManager;

    public function __construct(
        Gallery\Model\Repository\PictureRepository $pictureRepository,
        Aliases $aliases,
        UriAliasManager $uriAliasManager
    ) {
        $this->pictureRepository = $pictureRepository;
        $this->aliases = $aliases;
        $this->uriAliasManager = $uriAliasManager;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(ModelSaveEvent $event)
    {
        if ($event->isIsNewEntry() || $event->getModuleName() !== Gallery\Installer\Schema::MODULE_NAME) {
            return;
        }

        if ($this->isGallery($event)) {
            $galleryId = $event->getEntryId();
            $pictures = $this->pictureRepository->getPicturesByGalleryId($galleryId);

            $rawData = $event->getRawData();

            foreach ($pictures as $picture) {
                $this->uriAliasManager->insertUriAlias(
                    \sprintf(Gallery\Helpers::URL_KEY_PATTERN_PICTURE, $picture['id']),
                    !empty($rawData['alias']) ? $rawData['alias'] . '/img-' . $picture['id'] : '',
                    $rawData['seo_keywords'],
                    $rawData['seo_description'],
                    $rawData['seo_robots'],
                    $rawData['seo_title']
                );
            }
        }
    }

    /**
     * @return bool
     */
    private function isGallery(ModelSaveEvent $event)
    {
        $rawData = $event->getRawData();

        return isset(
                $rawData['alias'],
                $rawData['seo_title'],
                $rawData['seo_keywords'],
                $rawData['seo_description'],
                $rawData['seo_robots'],
                $rawData['seo_uri_pattern']
            ) && $rawData['seo_uri_pattern'] === Gallery\Helpers::URL_KEY_PATTERN_GALLERY;
    }
}