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
    const CACHE_ID = 'pics_id_';

    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    protected $pictureRepository;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    protected $config;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator
     */
    private $thumbnailGenerator;

    public function __construct(
        Core\Cache $cache,
        Core\Environment\ApplicationPath $appPath,
        PictureRepository $pictureRepository,
        Core\Settings\SettingsInterface $config,
        ThumbnailGenerator $thumbnailGenerator
    ) {
        parent::__construct($cache);

        $this->appPath = $appPath;
        $this->pictureRepository = $pictureRepository;
        $this->config = $config;
        $this->thumbnailGenerator = $thumbnailGenerator;
    }

    /**
     * Bindet die gecachete Galerie anhand ihrer ID ein.
     *
     * @param int $galleryId
     *
     * @return array
     *
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     */
    public function getCache(int $galleryId)
    {
        if ($this->cache->contains(self::CACHE_ID . $galleryId) === false) {
            $this->saveCache($galleryId);
        }

        return $this->cache->fetch(self::CACHE_ID . $galleryId);
    }

    /**
     * Erstellt den Galerie-Cache anhand der angegebenen ID.
     *
     * @param int $galleryId
     *
     * @return bool
     *
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     */
    public function saveCache(int $galleryId)
    {
        $pictures = $this->pictureRepository->getPicturesByGalleryId($galleryId);
        $cPictures = \count($pictures);

        for ($i = 0; $i < $cPictures; ++$i) {
            $cachedThumbnail = $this->cachePicture($pictures[$i]['file'], 'thumb');
            $cachedPicture = $this->cachePicture($pictures[$i]['file'], null);

            $pictures[$i]['width'] = $cachedThumbnail->getWidth();
            $pictures[$i]['height'] = $cachedThumbnail->getHeight();

            $pictures[$i]['uri_thumb'] = $cachedThumbnail->getFileWeb();
            $pictures[$i]['uri_picture'] = $cachedPicture->getFileWeb();
        }

        return $this->cache->save(self::CACHE_ID . $galleryId, $pictures);
    }

    /**
     * @param string      $fileName
     * @param null|string $action
     *
     * @return \ACP3\Core\Picture\Output
     *
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     */
    private function cachePicture(string $fileName, ?string $action): Output
    {
        $action = $action === 'thumb' ? 'thumb' : '';

        return $this->thumbnailGenerator->generateThumbnail($fileName, $action);
    }
}
