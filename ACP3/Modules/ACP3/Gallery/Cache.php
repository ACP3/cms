<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;
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
     * @param \ACP3\Core\Cache $cache
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository $pictureRepository
     * @param \ACP3\Core\Settings\SettingsInterface $config
     */
    public function __construct(
        Core\Cache $cache,
        Core\Environment\ApplicationPath $appPath,
        PictureRepository $pictureRepository,
        Core\Settings\SettingsInterface $config
    ) {
        parent::__construct($cache);

        $this->appPath = $appPath;
        $this->pictureRepository = $pictureRepository;
        $this->config = $config;
    }

    /**
     * Bindet die gecachete Galerie anhand ihrer ID ein
     *
     * @param integer $galleryId
     *
     * @return array
     */
    public function getCache($galleryId)
    {
        if ($this->cache->contains(self::CACHE_ID . $galleryId) === false) {
            $this->saveCache($galleryId);
        }

        return $this->cache->fetch(self::CACHE_ID . $galleryId);
    }

    /**
     * Erstellt den Galerie-Cache anhand der angegebenen ID
     *
     * @param integer $galleryId
     *
     * @return boolean
     */
    public function saveCache($galleryId)
    {
        $pictures = $this->pictureRepository->getPicturesByGalleryId($galleryId);
        $cPictures = \count($pictures);

        $settings = $this->config->getSettings(Schema::MODULE_NAME);

        for ($i = 0; $i < $cPictures; ++$i) {
            $pictures[$i]['width'] = $settings['thumbwidth'];
            $pictures[$i]['height'] = $settings['thumbheight'];
            $picInfos = @\getimagesize($this->appPath->getModulesDir() . 'gallery/' . $pictures[$i]['file']);
            if ($picInfos !== false) {
                if ($picInfos[0] > $settings['thumbwidth'] || $picInfos[1] > $settings['thumbheight']) {
                    $newHeight = $settings['thumbheight'];
                    $newWidth = (int)($picInfos[0] * $newHeight / $picInfos[1]);
                }

                $pictures[$i]['width'] = $newWidth ?? $picInfos[0];
                $pictures[$i]['height'] = $newHeight ?? $picInfos[1];
            }
        }

        return $this->cache->save(self::CACHE_ID . $galleryId, $pictures);
    }
}
