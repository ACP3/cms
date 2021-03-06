<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Services;

use ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;
use ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository;

class GalleryService implements GalleryServiceInterface
{
    /**
     * @var PictureRepository
     */
    private $pictureRepository;
    /**
     * @var ThumbnailGenerator
     */
    private $thumbnailGenerator;
    /**
     * @var GalleryRepository
     */
    private $galleryRepository;

    public function __construct(GalleryRepository $galleryRepository, PictureRepository $pictureRepository, ThumbnailGenerator $thumbnailGenerator)
    {
        $this->pictureRepository = $pictureRepository;
        $this->thumbnailGenerator = $thumbnailGenerator;
        $this->galleryRepository = $galleryRepository;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getGallery(int $galleryId): array
    {
        return $this->galleryRepository->getOneById($galleryId);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     * @throws \Doctrine\DBAL\Exception
     */
    public function getGalleryPictures(int $galleryId): array
    {
        $pictures = $this->pictureRepository->getPicturesByGalleryId($galleryId);

        foreach ($pictures as $i => $picture) {
            $cachedThumbnail = $this->thumbnailGenerator->generateThumbnail($picture['file'], 'thumb');
            $cachedPicture = $this->thumbnailGenerator->generateThumbnail($picture['file'], '');

            $pictures[$i]['width'] = $cachedThumbnail->getWidth();
            $pictures[$i]['height'] = $cachedThumbnail->getHeight();

            $pictures[$i]['uri_thumb'] = $cachedThumbnail->getFileWeb();
            $pictures[$i]['uri_picture'] = $cachedPicture->getFileWeb();
        }

        return $pictures;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     * @throws \Doctrine\DBAL\Exception
     */
    public function getGalleryWithPictures(int $galleryId): array
    {
        $gallery = $this->getGallery($galleryId);
        $gallery['pictures'] = $this->getGalleryPictures($galleryId);

        return $gallery;
    }
}
