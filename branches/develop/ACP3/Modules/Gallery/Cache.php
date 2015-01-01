<?php
namespace ACP3\Modules\Gallery;

use ACP3\Core;

/**
 * Class Cache
 * @package ACP3\Modules\Gallery
 */
class Cache extends Core\Modules\AbstractCacheStorage
{
    /**
     * @var string
     */
    const CACHE_ID = 'pics_id_';
    /**
     * @var Model
     */
    protected $galleryModel;
    /**
     * @var Core\Config
     */
    protected $galleryConfig;

    /**
     * @param Core\Cache $cache
     * @param Model $galleryModel
     * @param Core\Config $galleryConfig
     */
    public function __construct(
        Core\Cache $cache,
        Model $galleryModel,
        Core\Config $galleryConfig
    ) {
        parent::__construct($cache);

        $this->galleryModel = $galleryModel;
        $this->galleryConfig = $galleryConfig;
    }

    /**
     * Bindet die gecachete Galerie anhand ihrer ID ein
     *
     * @param integer $id
     *  Die ID der Galerie
     *
     * @return array
     */
    public function getCache($id)
    {
        if ($this->cache->contains(self::CACHE_ID . $id) === false) {
            $this->setCache($id);
        }

        return $this->cache->fetch(self::CACHE_ID . $id);
    }

    /**
     * Erstellt den Galerie-Cache anhand der angegebenen ID
     *
     * @param integer $id
     *  Die ID der zu cachenden Galerie
     *
     * @return boolean
     */
    public function setCache($id)
    {
        $pictures = $this->galleryModel->getPicturesByGalleryId($id);
        $c_pictures = count($pictures);

        $settings = $this->galleryConfig->getSettings();

        for ($i = 0; $i < $c_pictures; ++$i) {
            $pictures[$i]['width'] = $settings['thumbwidth'];
            $pictures[$i]['height'] = $settings['thumbheight'];
            $picInfos = @getimagesize(UPLOADS_DIR . 'gallery/' . $pictures[$i]['file']);
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
