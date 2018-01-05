<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Gallery;
use ACP3\Modules\ACP3\Seo\Core\Router\Aliases;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;

class GeneratePictureUriAliasesOnModelAfterSaveListener
{
    /**
     * @var Gallery\Model\Repository\GalleryPicturesRepository
     */
    private $pictureRepository;
    /**
     * @var Aliases
     */
    private $aliases;
    /**
     * @var MetaStatements
     */
    private $metaStatements;
    /**
     * @var UriAliasManager
     */
    private $uriAliasManager;

    /**
     * UpdateUriAliasesOnModelAfterSaveListener constructor.
     * @param Gallery\Model\Repository\GalleryPicturesRepository $pictureRepository
     */
    public function __construct(Gallery\Model\Repository\GalleryPicturesRepository $pictureRepository)
    {
        $this->pictureRepository = $pictureRepository;
    }

    /**
     * @param \ACP3\Modules\ACP3\Seo\Core\Router\Aliases $aliases
     */
    public function setAliases(Aliases $aliases)
    {
        $this->aliases = $aliases;
    }

    /**
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaStatements $metaStatements
     */
    public function setMetaStatements(MetaStatements $metaStatements)
    {
        $this->metaStatements = $metaStatements;
    }

    /**
     * @param \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager $uriAliasManager
     */
    public function setUriAliasManager(UriAliasManager $uriAliasManager)
    {
        $this->uriAliasManager = $uriAliasManager;
    }

    /**
     * @param ModelSaveEvent $event
     */
    public function generatePictureAliases(ModelSaveEvent $event)
    {
        if ($event->isIsNewEntry() || $event->getModuleName() !== Gallery\Installer\Schema::MODULE_NAME) {
            return;
        }

        if ($this->hasAllRequiredDependencies() && $this->isGallery($event)) {
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
    private function hasAllRequiredDependencies()
    {
        return $this->aliases && $this->metaStatements && $this->uriAliasManager;
    }

    /**
     * @param ModelSaveEvent $event
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
