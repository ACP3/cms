<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;
use ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository;
use Psr\Container\ContainerInterface;

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
     * @var \Psr\Container\ContainerInterface
     */
    private $container;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator
     */
    private $thumbnailGenerator;

    public function __construct(
        Core\Cache $cache,
        Core\Environment\ApplicationPath $appPath,
        PictureRepository $pictureRepository,
        Core\Settings\SettingsInterface $config,
        ThumbnailGenerator $thumbnailGenerator,
        ContainerInterface $container
    ) {
        parent::__construct($cache);

        $this->appPath = $appPath;
        $this->pictureRepository = $pictureRepository;
        $this->config = $config;
        $this->container = $container;
        $this->thumbnailGenerator = $thumbnailGenerator;
    }

    /**
     * Bindet die gecachete Galerie anhand ihrer ID ein.
     *
     * @param int $galleryId
     *
     * @return array
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
     */
    public function saveCache(int $galleryId)
    {
        $pictures = $this->pictureRepository->getPicturesByGalleryId($galleryId);
        $cPictures = \count($pictures);

        $settings = $this->config->getSettings(Schema::MODULE_NAME);

        for ($i = 0; $i < $cPictures; ++$i) {
            $cachedThumbnail = $this->cachePicture($pictures[$i]['file'], 'thumb');
            $cachedPicture = $this->cachePicture($pictures[$i]['file'], null);

            $pictures[$i]['width'] = $settings['thumbwidth'];
            $pictures[$i]['height'] = $settings['thumbheight'];
            $picInfos = @\getimagesize($cachedThumbnail->getFileWeb());
            if ($picInfos !== false) {
                $pictures[$i]['width'] = $picInfos[0];
                $pictures[$i]['height'] = $picInfos[1];
            }

            $pictures[$i]['uri_thumb'] = $cachedThumbnail->getFileWeb();
            $pictures[$i]['uri_picture'] = $cachedPicture->getFileWeb();
        }

        return $this->cache->save(self::CACHE_ID . $galleryId, $pictures);
    }

    private function cachePicture(string $fileName, ?string $action): Core\Picture
    {
        $action = $action === 'thumb' ? 'thumb' : '';

        /** @var Core\Picture $image */
        $image = $this->container->get('core.image');
        $this->thumbnailGenerator->generateThumbnail($image, $action, $fileName);

        $image->process();
        $image->freeMemory();

        return $image;
    }
}
