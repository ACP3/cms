<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
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
     * @var Gallery\Model\Repository\PictureRepository
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
     * @param Gallery\Model\Repository\PictureRepository $pictureRepository
     */
    public function __construct(Gallery\Model\Repository\PictureRepository $pictureRepository)
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
        if ($this->aliases && $this->metaStatements && $this->uriAliasManager) {
            $galleryId = $event->getEntryId();
            $pictures = $this->pictureRepository->getPicturesByGalleryId($galleryId);

            $alias = $this->aliases->getUriAlias(sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $galleryId), true);
            $seoKeywords = $this->metaStatements->getKeywords(sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $galleryId));
            $seoDescription = $this->metaStatements->getDescription(sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $galleryId));

            foreach ($pictures as $picture) {
                $this->uriAliasManager->insertUriAlias(
                    sprintf(Gallery\Helpers::URL_KEY_PATTERN_PICTURE, $picture['id']),
                    !empty($alias) ? $alias . '/img-' . $picture['id'] : '',
                    $seoKeywords,
                    $seoDescription
                );
            }
        }
    }
}
