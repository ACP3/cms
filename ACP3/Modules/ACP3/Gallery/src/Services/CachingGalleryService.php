<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Services;

use ACP3\Core\Cache;

class CachingGalleryService implements GalleryServiceInterface
{
    public const CACHE_ID_GALLERY_PICTURES = 'gallery_pics_%d';

    /**
     * @var Cache
     */
    private $galleryCache;
    /**
     * @var GalleryService
     */
    private $galleryService;

    public function __construct(Cache $galleryCache, GalleryService $galleryService)
    {
        $this->galleryCache = $galleryCache;
        $this->galleryService = $galleryService;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getGallery(int $galleryId): array
    {
        return $this->galleryService->getGallery($galleryId);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     * @throws \Doctrine\DBAL\Exception
     */
    public function getGalleryPictures(int $galleryId): array
    {
        $cacheKey = sprintf(self::CACHE_ID_GALLERY_PICTURES, $galleryId);

        if (!$this->galleryCache->contains($cacheKey)) {
            $this->galleryCache->save($cacheKey, $this->galleryService->getGalleryPictures($galleryId));
        }

        return $this->galleryCache->fetch($cacheKey);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     * @throws \Doctrine\DBAL\Exception
     */
    public function getGalleryWithPictures(int $galleryId): array
    {
        return $this->galleryService->getGalleryWithPictures($galleryId);
    }
}
