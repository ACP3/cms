<?php
namespace ACP3\Modules\ACP3\Gallery;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository;

/**
 * Class Cache
 * @package ACP3\Modules\ACP3\Gallery
 */
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
     * @var \ACP3\Core\Config
     */
    protected $config;

    /**
     * @param \ACP3\Core\Cache                                   $cache
     * @param \ACP3\Core\Environment\ApplicationPath             $appPath
     * @param \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository $pictureRepository
     * @param \ACP3\Core\Config                                  $config
     */
    public function __construct(
        Core\Cache $cache,
        Core\Environment\ApplicationPath $appPath,
        PictureRepository $pictureRepository,
        Core\Config $config
    ) {
        parent::__construct($cache);

        $this->appPath = $appPath;
        $this->pictureRepository = $pictureRepository;
        $this->config = $config;
    }

    /**
     * Bindet die gecachete Galerie anhand ihrer ID ein
     *
     * @param integer $id
     *
     * @return array
     */
    public function getCache($id)
    {
        if ($this->cache->contains(self::CACHE_ID . $id) === false) {
            $this->saveCache($id);
        }

        return $this->cache->fetch(self::CACHE_ID . $id);
    }

    /**
     * Erstellt den Galerie-Cache anhand der angegebenen ID
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function saveCache($id)
    {
        $pictures = $this->pictureRepository->getPicturesByGalleryId($id);
        $cPictures = count($pictures);

        $settings = $this->config->getSettings('gallery');

        for ($i = 0; $i < $cPictures; ++$i) {
            $pictures[$i]['width'] = $settings['thumbwidth'];
            $pictures[$i]['height'] = $settings['thumbheight'];
            $picInfos = @getimagesize($this->appPath->getModulesDir() . 'gallery/' . $pictures[$i]['file']);
            if ($picInfos !== false) {
                if ($picInfos[0] > $settings['thumbwidth'] || $picInfos[1] > $settings['thumbheight']) {
                    $newHeight = $settings['thumbheight'];
                    $newWidth = intval($picInfos[0] * $newHeight / $picInfos[1]);
                }

                $pictures[$i]['width'] = isset($newWidth) ? $newWidth : $picInfos[0];
                $pictures[$i]['height'] = isset($newHeight) ? $newHeight : $picInfos[1];
            }
        }

        return $this->cache->save(self::CACHE_ID . $id, $pictures);
    }
}
