<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
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
     * @var Gallery\Model\Repository\PictureRepository
     */
    private $pictureRepository;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Core\Router\Aliases|null
     */
    private $aliases;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaStatements|null
     */
    private $metaStatements;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager|null
     */
    private $uriAliasManager;

    /**
     * UpdateUriAliasesOnModelAfterSaveListener constructor.
     *
     * @param Gallery\Model\Repository\PictureRepository         $pictureRepository
     * @param \ACP3\Modules\ACP3\Seo\Core\Router\Aliases|null    $aliases
     * @param \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager|null $uriAliasManager
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaStatements|null  $metaStatements
     */
    public function __construct(
        Gallery\Model\Repository\PictureRepository $pictureRepository,
        ?Aliases $aliases = null,
        ?UriAliasManager $uriAliasManager = null,
        ?MetaStatements $metaStatements = null)
    {
        $this->pictureRepository = $pictureRepository;
        $this->aliases = $aliases;
        $this->uriAliasManager = $uriAliasManager;
        $this->metaStatements = $metaStatements;
    }

    /**
     * @param ModelSaveEvent $event
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(ModelSaveEvent $event)
    {
        if (!$event->isIsNewEntry()) {
            return;
        }

        if ($this->aliases && $this->metaStatements && $this->uriAliasManager) {
            $pictureId = $event->getEntryId();

            $galleryId = $this->pictureRepository->getGalleryIdFromPictureId($pictureId);
            $alias = $this->aliases->getUriAlias(\sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $galleryId), true);
            if (!empty($alias)) {
                $alias .= '/img-' . $pictureId;
            }
            $seoKeywords = $this->metaStatements->getKeywords(
                \sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $galleryId)
            );
            $seoDescription = $this->metaStatements->getDescription(
                \sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $galleryId)
            );
            $seoRobots = $this->metaStatements->getRobotsSetting(
                \sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $galleryId)
            );

            $robotsSetting = \array_flip($this->metaStatements->getRobotsMap());

            $this->uriAliasManager->insertUriAlias(
                \sprintf(Gallery\Helpers::URL_KEY_PATTERN_PICTURE, $pictureId),
                $alias,
                $seoKeywords,
                $seoDescription,
                $robotsSetting[$seoRobots] ?? 0
            );
        }
    }
}
