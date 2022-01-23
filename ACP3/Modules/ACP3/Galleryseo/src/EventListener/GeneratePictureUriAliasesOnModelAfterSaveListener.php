<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Galleryseo\EventListener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Gallery;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;
use ACP3\Modules\ACP3\Seo\Installer\Schema as SeoSchema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GeneratePictureUriAliasesOnModelAfterSaveListener implements EventSubscriberInterface
{
    public function __construct(private Modules $modules, private Gallery\Repository\PictureRepository $pictureRepository, private UriAliasManager $uriAliasManager)
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

        if ($event->isIsNewEntry() || $event->getModuleName() !== Gallery\Installer\Schema::MODULE_NAME) {
            return;
        }

        if ($this->isGallery($event)) {
            $galleryId = $event->getEntryId();
            $pictures = $this->pictureRepository->getPicturesByGalleryId($galleryId);

            $rawData = $event->getRawData();

            foreach ($pictures as $picture) {
                $this->uriAliasManager->insertUriAlias(
                    sprintf(Gallery\Helpers::URL_KEY_PATTERN_PICTURE, $picture['id']),
                    !empty($rawData['alias']) ? $rawData['alias'] . '/img-' . $picture['id'] : '',
                    $rawData['seo_keywords'],
                    $rawData['seo_description'],
                    $rawData['seo_robots'],
                    $rawData['seo_title']
                );
            }
        }
    }

    private function isGallery(ModelSaveEvent $event): bool
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

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'core.model.after_save' => ['__invoke', -250],
        ];
    }
}
