<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Services;

use Psr\Cache\CacheItemPoolInterface;

class CachingGalleryService implements GalleryServiceInterface
{
    public const CACHE_ID_GALLERY_PICTURES = 'gallery_pics_%d';

    /**
     * @var CacheItemPoolInterface
     */
    private $galleryCachePool;
    /**
     * @var GalleryService
     */
    private $galleryService;

    public function __construct(CacheItemPoolInterface $galleryCachePool, GalleryService $galleryService)
    {
        $this->galleryCachePool = $galleryCachePool;
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
        $cacheItem = $this->galleryCachePool->getItem($cacheKey);

        if (!$cacheItem->isHit()) {
            $cacheItem->set($this->galleryService->getGalleryPictures($galleryId));
            $this->galleryCachePool->saveDeferred($cacheItem);
        }

        return $cacheItem->get();
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
