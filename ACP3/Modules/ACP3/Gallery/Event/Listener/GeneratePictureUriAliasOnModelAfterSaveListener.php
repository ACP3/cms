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

class GeneratePictureUriAliasOnModelAfterSaveListener
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
    public function generatePictureAlias(ModelSaveEvent $event)
    {
        if (!$event->isIsNewEntry()) {
            return;
        }

        if ($this->aliases && $this->metaStatements && $this->uriAliasManager) {
            $pictureId = $event->getEntryId();

            $galleryId = $this->pictureRepository->getGalleryIdFromPictureId($pictureId);
            $alias = $this->aliases->getUriAlias(sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $galleryId), true);
            if (!empty($alias)) {
                $alias .= '/img-' . $pictureId;
            }
            $seoKeywords = $this->metaStatements->getKeywords(
                sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $galleryId)
            );
            $seoDescription = $this->metaStatements->getDescription(
                sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $galleryId)
            );
            $seoRobots = $this->metaStatements->getRobotsSetting(
                sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $galleryId)
            );

            $this->uriAliasManager->insertUriAlias(
                sprintf(Gallery\Helpers::URL_KEY_PATTERN_PICTURE, $pictureId),
                $alias,
                $seoKeywords,
                $seoDescription,
                $seoRobots
            );
        }
    }
}
