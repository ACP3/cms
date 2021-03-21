<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery;

use ACP3\Core;
use ACP3\Core\Picture\Output;
use ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator;
use ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository;

class Cache extends Core\Modules\AbstractCacheStorage
{
    /**
     * @var string
     */
    public const CACHE_ID = 'pics_id_';

    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    private $pictureRepository;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator
     */
    private $thumbnailGenerator;

    public function __construct(
        Core\Cache $cache,
        PictureRepository $pictureRepository,
        ThumbnailGenerator $thumbnailGenerator
    ) {
        parent::__construct($cache);

        $this->pictureRepository = $pictureRepository;
        $this->thumbnailGenerator = $thumbnailGenerator;
    }

    /**
     * Bindet die gecachete Galerie anhand ihrer ID ein.
     *
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     * @throws \Doctrine\DBAL\Exception
     */
    public function getCache(int $galleryId): array
    {
        if ($this->cache->contains(self::CACHE_ID . $galleryId) === false) {
            $this->saveCache($galleryId);
        }

        return $this->cache->fetch(self::CACHE_ID . $galleryId);
    }

    /**
     * Erstellt den Galerie-Cache anhand der angegebenen ID.
     *
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     * @throws \Doctrine\DBAL\Exception
     */
    public function saveCache(int $galleryId): bool
    {
        $pictures = $this->pictureRepository->getPicturesByGalleryId($galleryId);

        foreach ($pictures as $i => $picture) {
            $cachedThumbnail = $this->cachePicture($picture['file'], 'thumb');
            $cachedPicture = $this->cachePicture($picture['file'], null);

            $pictures[$i]['width'] = $cachedThumbnail->getWidth();
            $pictures[$i]['height'] = $cachedThumbnail->getHeight();

            $pictures[$i]['uri_thumb'] = $cachedThumbnail->getFileWeb();
            $pictures[$i]['uri_picture'] = $cachedPicture->getFileWeb();
        }

        return $this->cache->save(self::CACHE_ID . $galleryId, $pictures);
    }

    /**
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     */
    private function cachePicture(string $fileName, ?string $action): Output
    {
        $action = $action === 'thumb' ? 'thumb' : '';

        return $this->thumbnailGenerator->generateThumbnail($fileName, $action);
    }
}
