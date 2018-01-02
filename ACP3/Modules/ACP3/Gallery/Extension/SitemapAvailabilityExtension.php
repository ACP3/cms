<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Extension;

use ACP3\Core\Date;
use ACP3\Core\Router\RouterInterface;
use ACP3\Modules\ACP3\Gallery\Helpers;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryPicturesRepository;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;
use ACP3\Modules\ACP3\Seo\Extension\AbstractSitemapAvailabilityExtension;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;

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
     * @var GalleryPicturesRepository
     */
    protected $pictureRepository;

    /**
     * SitemapAvailabilityExtension constructor.
     * @param Date $date
     * @param RouterInterface $router
     * @param GalleryRepository $galleryRepository
     * @param GalleryPicturesRepository $pictureRepository
     * @param MetaStatements $metaStatements
     */
    public function __construct(
        Date $date,
        RouterInterface $router,
        GalleryRepository $galleryRepository,
        GalleryPicturesRepository $pictureRepository,
        MetaStatements $metaStatements
    ) {
        parent::__construct($router, $metaStatements);

        $this->date = $date;
        $this->galleryRepository = $galleryRepository;
        $this->pictureRepository = $pictureRepository;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return Schema::MODULE_NAME;
    }

    /**
     * @inheritdoc
     */
    protected function fetchSitemapUrls($isSecure = null)
    {
        $this->addUrl('gallery/index/index', null, $isSecure);

        foreach ($this->galleryRepository->getAll($this->date->getCurrentDateTime()) as $result) {
            $this->addUrl(
                sprintf(Helpers::URL_KEY_PATTERN_GALLERY, $result['id']),
                $this->date->toDateTime($result['updated_at']),
                $isSecure
            );

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
