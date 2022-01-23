<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Galleryseo\EventListener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Modules;
use ACP3\Core\SEO\MetaStatementsServiceInterface;
use ACP3\Modules\ACP3\Gallery;
use ACP3\Modules\ACP3\Seo\Core\Router\Aliases;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;
use ACP3\Modules\ACP3\Seo\Installer\Schema as SeoSchema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GeneratePictureUriAliasOnModelAfterSaveListener implements EventSubscriberInterface
{
    public function __construct(private Modules $modules, private Gallery\Repository\PictureRepository $pictureRepository, private Aliases $aliases, private UriAliasManager $uriAliasManager, private MetaStatementsServiceInterface $metaStatements)
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(ModelSaveEvent $event): void
    {
        if (!$this->modules->isInstalled(SeoSchema::MODULE_NAME)) {
            return;
        }

        if (!$event->isIsNewEntry()) {
            return;
        }

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

        $robotsSetting = array_flip($this->metaStatements->getRobotsMap());

        $this->uriAliasManager->insertUriAlias(
            sprintf(Gallery\Helpers::URL_KEY_PATTERN_PICTURE, $pictureId),
            $alias,
            $seoKeywords,
            $seoDescription,
            $robotsSetting[$seoRobots] ?? 0
        );
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'gallery.model.gallery_pictures.after_save' => ['__invoke', -250],
        ];
    }
}
