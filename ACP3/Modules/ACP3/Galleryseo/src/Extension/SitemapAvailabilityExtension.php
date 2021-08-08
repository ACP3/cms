<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Galleryseo\Extension;

use ACP3\Core\Date;
use ACP3\Core\Router\RouterInterface;
use ACP3\Core\SEO\MetaStatementsServiceInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Gallery\Helpers;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;
use ACP3\Modules\ACP3\Gallery\Repository\GalleryRepository;
use ACP3\Modules\ACP3\Gallery\Repository\PictureRepository;
use ACP3\Modules\ACP3\Seo\Extension\AbstractSitemapAvailabilityExtension;

class SitemapAvailabilityExtension extends AbstractSitemapAvailabilityExtension
{
    /**
     * @var Date
     */
    protected $date;
    /**
     * @var GalleryRepository
     */
    protected $galleryRepository;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Repository\PictureRepository
     */
    protected $pictureRepository;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;

    public function __construct(
        Date $date,
        RouterInterface $router,
        SettingsInterface $settings,
        GalleryRepository $galleryRepository,
        PictureRepository $pictureRepository,
        MetaStatementsServiceInterface $metaStatements
    ) {
        parent::__construct($router, $metaStatements);

        $this->date = $date;
        $this->galleryRepository = $galleryRepository;
        $this->pictureRepository = $pictureRepository;
        $this->settings = $settings;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return Schema::MODULE_NAME;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function fetchSitemapUrls($isSecure = null)
    {
        $gallerySettings = $this->settings->getSettings(Schema::MODULE_NAME);

        $this->addUrl('gallery/index/index', null, $isSecure);

        foreach ($this->galleryRepository->getAll($this->date->getCurrentDateTime()) as $result) {
            $this->addUrl(
                sprintf(Helpers::URL_KEY_PATTERN_GALLERY, $result['id']),
                $this->date->toDateTime($result['updated_at']),
                $isSecure
            );

            if (((int) $gallerySettings['overlay']) === 0) {
                foreach ($this->pictureRepository->getPicturesByGalleryId($result['id']) as $picture) {
                    $this->addUrl(
                        sprintf(Helpers::URL_KEY_PATTERN_PICTURE, $picture['id']),
                        $this->date->toDateTime($result['updated_at']),
                        $isSecure
                    );
                }
            }
        }
    }
}
