<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Services;

use ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator;
use ACP3\Modules\ACP3\Gallery\Repository\GalleryRepository;
use ACP3\Modules\ACP3\Gallery\Repository\PictureRepository;

class GalleryService implements GalleryServiceInterface
{
    public function __construct(private readonly GalleryRepository $galleryRepository, private readonly PictureRepository $pictureRepository, private readonly ThumbnailGenerator $thumbnailGenerator)
    {
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

            $pictures[$i]['thumbnail_width'] = $cachedThumbnail->getWidth();
            $pictures[$i]['thumbnail_height'] = $cachedThumbnail->getHeight();
            $pictures[$i]['width'] = $cachedPicture->getWidth();
            $pictures[$i]['height'] = $cachedPicture->getHeight();

            $pictures[$i]['uri_thumbnail'] = $cachedThumbnail->getFileWeb();
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
