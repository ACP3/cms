<?php
namespace ACP3\Modules\ACP3\Gallery;

use ACP3\Core;

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
     * @var \ACP3\Modules\ACP3\Gallery\Model
     */
    protected $galleryModel;
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;

    /**
     * @param Core\Cache  $cache
     * @param Model       $galleryModel
     * @param Core\Config $config
     */
    public function __construct(
        Core\Cache $cache,
        Model $galleryModel,
        Core\Config $config
    )
    {
        parent::__construct($cache);

        $this->galleryModel = $galleryModel;
        $this->config = $config;
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

        $settings = $this->config->getSettings('gallery');

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
